<?php include 'header.php'?>
<div id="center_align">
    <h1>Ordayna</h1>
    <h2>Bejelentkezés</h2>
    <form>
        <label for="login_email">E-mail</label><br>
        <input type="email" id="login_email"><br>

        <label for="login_pass">Jelszó</label><br>
        <input type="password" id="login_pass"><br>
    </form><div id="please_be_centered">
    <button onclick="location.href='index.php'" id="login_button">Belépés</button><br>
    <button onclick="location.href='signup.php'" id="to_signup_button">Regisztráció</button></div>
</div>
<button onclick="location.href='profile.php'" id="user_button">Felhasználó</button>