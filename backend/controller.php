<?php

static $is_test_server = php_sapi_name() === "cli-server";

use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\UnencryptedToken;

include "db.php";
include "jwt.php";

class UserController
{

    private $jwt;

    function __construct()
    {
        $this->jwt = new JWT();
    }

    /**
     * Returns null on failure and users in json format on success
     */
    public function getAllUsers(): mixed
    {
        if (!isset($_GET["intezmeny_id"]) or intval($_GET["intezmeny_id"]) === 0) {
            return null;
        }

        $intezmeny_id = intval($_GET["intezmeny_id"]);

        $db = new DB();
        $res = $db->getAllUsers($intezmeny_id);

        header('Content-Type: application/json');
        return json_encode($res->fetch_all());
    }

    public function getRefreshToken(): ControllerRet
    {
        global $is_test_server;

        $data = json_decode(file_get_contents("php://input"));

        if (
            !isset($data->email) or !is_string($data->email) or !preg_match('/^[^@]+[@]+[^@]+$/', $data->email) or
            !isset($data->pass) or !is_string($data->pass)
        ) {
            return ControllerRet::bad_request;
        }

        $db = new DB();

        if (!$db->userExistsEmail($data->email)) {
            return ControllerRet::user_does_not_exist;
        }

        $user_pass = $db->getUserPassViaEmail($data->email);
        if (!$user_pass or !password_verify($data->pass, $user_pass)) {
            return ControllerRet::unauthorised;
        }

        $user_id = $db->getUserIdViaEmail($data->email);
        $refresh_token = $this->jwt->createRefreshToken($user_id);

        $arr_cookie_options = array(
            'expires' => time() + 60 * 60 * 24 * 15,
            'path' => '/token/',
            'domain' => '',
            'secure' => !$is_test_server,
            'httponly' => true,
            'samesite' => 'Strict'
        );
        setcookie('RefreshToken', $refresh_token->toString(), $arr_cookie_options);

        return ControllerRet::success;
    }

    public function refreshRefreshToken(): ControllerRet
    {
        global $is_test_server;
        $db = new DB();

        $token = $this->validateRefreshToken($db);
        if (is_a($token, "ControllerRet")) {
            return $token;
        }
        $user_id = $token->claims()->get("uid");
        $new_token = $this->jwt->createRefreshToken($user_id);

        // Expires after 15 days
        $db->newInvalidRefreshToken($token->claims()->get(RegisteredClaims::ID), '15 0:0:0');

        $arr_cookie_options = array(
            // 15 days
            'expires' => time() + 60 * 60 * 24 * 15,
            'path' => '/token/',
            'domain' => '',
            'secure' => !$is_test_server,
            'httponly' => true,
            'samesite' => 'Strict'
        );
        setcookie('RefreshToken', $new_token->toString(), $arr_cookie_options);

        return ControllerRet::success;
    }

    public function getAccessToken(): ControllerRet
    {
        global $is_test_server;
        $db = new DB();

        $token = $this->validateRefreshToken($db);
        if (is_a($token, "ControllerRet")) {
            return $token;
        }
        $user_id = $token->claims()->get("uid");
        $new_access_token = $this->jwt->createAccessToken($user_id);

        $arr_cookie_options = array(
            // 10 minutes
            'expires' => time() + 60 * 10,
            'path' => '/',
            'domain' => '',
            'secure' => !$is_test_server,
            'httponly' => true,
            'samesite' => 'Strict'
        );
        setcookie('AccessToken', $new_access_token->toString(), $arr_cookie_options);

        return ControllerRet::success;
    }

    public function createUser(): ControllerRet
    {
        $data = json_decode(file_get_contents("php://input"));
        if (
            !isset($data->disp_name) or !is_string($data->disp_name) or
            !isset($data->email) or !is_string($data->email) or !preg_match('/^[^@]+[@]+[^@]+$/', $data->email) or
            !isset($data->pass) or !is_string($data->pass) or strlen($data->pass) < 8
        ) {
            return ControllerRet::bad_request;
        }

        $phone_number = null;
        if (isset($data->phone_number)) {
            if (!is_string($data->phone_number) or strlen($data->phone_number) > 15 or !is_numeric($data->phone_number)) {
                return ControllerRet::bad_request;
            }
            $phone_number = $data->phone_number;
        }

        $db = new DB();

        if ($db->userExistsEmail($data->email)) {
            return ControllerRet::user_already_exists;
        }

        $pass_hash = password_hash($data->pass, PASSWORD_BCRYPT);
        if (!$db->createUser($data->disp_name, $data->email, $phone_number, $pass_hash)) {
            return ControllerRet::unexpected_error;
        }

        return ControllerRet::success_created;
    }

    public function deleteUser(): ControllerRet
    {
        $token = $this->validateAccessToken();
        if (is_a($token, "ControllerRet")) {
            return $token;
        }
        $user_id = $token->claims()->get("uid");

        $db = new DB();

        if (!$db->userExistsViaId($user_id)) {
            return ControllerRet::user_does_not_exist;
        }

        if (!$db->deleteUserViaId($user_id)) {
            return ControllerRet::unexpected_error;
        }

        // Unset token cookies
        setcookie('RefreshToken', "", 0);
        setcookie('AccessToken', "", 0);

        return ControllerRet::success_no_content;
    }

    public function changeDisplayName(): ControllerRet
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->new_disp_name) or !is_string($data->new_disp_name) or strlen($data->new_disp_name) > 200) {
            return ControllerRet::bad_request;
        }

        $token = $this->validateAccessToken();
        if (is_a($token, "ControllerRet")) {
            return $token;
        }
        $user_id = $token->claims()->get("uid");

        $db = new DB();

        if (!$db->userExistsViaId($user_id)) {
            return ControllerRet::user_does_not_exist;
        }

        if (!$db->changeDisplayNameViaId($user_id, $data->new_disp_name)) {
            return ControllerRet::unexpected_error;
        }

        return ControllerRet::success_no_content;
    }

    public function changePhoneNumber(): ControllerRet
    {
        $data = json_decode(file_get_contents("php://input"));
        if (
            !isset($data->new_phone_number) or !is_string($data->new_phone_number) or
            strlen($data->new_phone_number) > 15 or !is_numeric($data->new_phone_number)
        ) {
            return ControllerRet::bad_request;
        }

        $token = $this->validateAccessToken();
        if (is_a($token, "ControllerRet")) {
            return $token;
        }
        $user_id = $token->claims()->get("uid");

        $db = new DB();

        if (!$db->userExistsViaId($user_id)) {
            return ControllerRet::user_does_not_exist;
        }

        if (!$db->changePhoneNumberViaId($user_id, $data->new_phone_number)) {
            return ControllerRet::unexpected_error;
        }

        return ControllerRet::success_no_content;
    }

    public function changePassword(): ControllerRet
    {
        $data = json_decode(file_get_contents("php://input"));
        if (
            !isset($data->new_pass) or !is_string($data->new_pass) or strlen($data->new_pass) < 8
        ) {
            return ControllerRet::bad_request;
        }

        $token = $this->validateAccessToken();
        if (is_a($token, "ControllerRet")) {
            return $token;
        }
        $user_id = $token->claims()->get("uid");

        $db = new DB();

        if (!$db->userExistsViaId($user_id)) {
            return ControllerRet::user_does_not_exist;
        }

        if (!$db->changePasswordHashViaId($user_id, password_hash($data->new_pass, PASSWORD_BCRYPT))) {
            return ControllerRet::unexpected_error;
        }

        return ControllerRet::success_no_content;
    }

    public function createIntezmeny(): ControllerRet
    {
        $data = json_decode(file_get_contents("php://input"));
        if (
            !isset($data->intezmeny_name) or !is_string($data->intezmeny_name) or strlen($data->intezmeny_name) > 200
        ) {
            return ControllerRet::bad_request;
        }

        $token = $this->validateAccessToken();
        if (is_a($token, "ControllerRet")) {
            return $token;
        }
        $user_id = $token->claims()->get("uid");

        $db = new DB();

        if (!$db->userExistsViaId($user_id)) {
            return ControllerRet::user_does_not_exist;
        }

        $ret = $db->createIntezmeny($data->intezmeny_name, $user_id);
        if (!$ret) {
            return ControllerRet::unexpected_error;
        }

        return ControllerRet::success_created;
    }

    public function deleteIntezmeny(): ControllerRet
    {
        $data = json_decode(file_get_contents("php://input"));
        if (
            !isset($data->intezmeny_id) or !is_string($data->intezmeny_id) or !is_numeric($data->intezmeny_id)
        ) {
            return ControllerRet::bad_request;
        }

        $token = $this->validateAccessToken();
        if (is_a($token, "ControllerRet")) {
            return $token;
        }
        $user_id = $token->claims()->get("uid");

        $db = new DB();

        if (!$db->userExistsViaId($user_id)) {
            return ControllerRet::user_does_not_exist;
        }

        if (!$db->partOfIntezmeny($user_id, $data->intezmeny_id)) {
            return ControllerRet::unauthorised;
        }

        $ret = $db->deleteIntezmeny($data->intezmeny_id);
        if (!$ret) {
            return ControllerRet::unexpected_error;
        }

        return ControllerRet::success_no_content;
    }

    public function getIntezmenys(): ControllerRet
    {
        $token = $this->validateAccessToken();
        if (is_a($token, "ControllerRet")) {
            return $token;
        }
        $user_id = $token->claims()->get("uid");

        $db = new DB();

        if (!$db->userExistsViaId($user_id)) {
            return ControllerRet::user_does_not_exist;
        }

        $ret = $db->getIntezmenys($token->claims()->get("uid"));
        if (!$ret) {
            return ControllerRet::unexpected_error;
        }

        header('Content-Type: application/json');
        echo json_encode($ret->fetch_all());

        return ControllerRet::success;
    }

    public function getClasses(): ControllerRet
    {
        $data = json_decode(file_get_contents("php://input"));
        if (
            !isset($data->intezmeny_id) or !is_string($data->intezmeny_id) or !is_numeric($data->intezmeny_id)
        ) {
            return ControllerRet::bad_request;
        }

        $token = $this->validateAccessToken();
        if (is_a($token, "ControllerRet")) {
            return $token;
        }
        $user_id = $token->claims()->get("uid");

        $db = new DB();

        if (!$db->userExistsViaId($user_id)) {
            return ControllerRet::user_does_not_exist;
        }

        if (!$db->partOfIntezmeny($user_id, $data->intezmeny_id)) {
            return ControllerRet::unauthorised;
        }

        $ret = $db->getClasses($data->intezmeny_id);
        if (!$ret) {
            return ControllerRet::unexpected_error;
        }

        header('Content-Type: application/json');
        echo json_encode($ret->fetch_all());

        return ControllerRet::success;
    }

    function validateRefreshToken(DB $db): ControllerRet|UnencryptedToken
    {
        if (!isset($_COOKIE["RefreshToken"]) or !is_string($_COOKIE["RefreshToken"])) {
            return ControllerRet::bad_request;
        }

        $token = $this->jwt->parseToken($_COOKIE["RefreshToken"]);
        if ($token === null) {
            return ControllerRet::bad_request;
        }

        $invalid_ids = $db->getRevokedRefreshTokens();
        if (!$this->jwt->validateRefreshToken($token, $invalid_ids)) {
            return ControllerRet::unauthorised;
        }

        return $token;
    }

    function validateAccessToken(): ControllerRet|UnencryptedToken
    {
        if (!isset($_COOKIE["AccessToken"]) or !is_string($_COOKIE["AccessToken"])) {
            return ControllerRet::bad_request;
        }

        $token = $this->jwt->parseToken($_COOKIE["AccessToken"]);
        if ($token === null) {
            return ControllerRet::bad_request;
        }

        if (!$this->jwt->validateAccessToken($token)) {
            return ControllerRet::unauthorised;
        }
        return $token;
    }
}

function handleReturn(ControllerRet $ret_val): void
{
    switch ($ret_val) {
        case ControllerRet::success:
            http_response_code(200);
            break;
        case ControllerRet::success_created:
            http_response_code(201);
            break;
        case ControllerRet::success_no_content:
            http_response_code(204);
            break;
        case ControllerRet::bad_request:
            http_response_code(400);
            echo "Bad request";
            break;
        case ControllerRet::user_does_not_exist:
            http_response_code(400);
            echo "User does not exist";
            break;
        case ControllerRet::user_already_exists:
            http_response_code(400);
            echo "User already exists";
            break;
        case ControllerRet::unauthorised:
            http_response_code(403);
            echo "Unauthorised";
            break;
        case ControllerRet::unexpected_error:
            http_response_code(500);
            echo "Unexpected error";
            break;
    }
}

enum ControllerRet
{
    case success;
    case success_created;
    case success_no_content;
    case bad_request;
    case unauthorised;
    case user_does_not_exist;
    case user_already_exists;
    case unexpected_error;
}
