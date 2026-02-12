<?php include 'header.php'?>
<div id="center_align">
<h1>Ordayna</h1>
<h2>Regisztráció</h2>
<form>

    <label for="signup_display_name">Felhasználónév</label><br>
    <input type="text" id="signup_display_name"><br>

    <label for="signup_email">E-mail</label><br>
    <input type="email" id="signup_email"><br>

    <label for="signup_tel">Telefonszám</label><br>
    <input type="text" id="signup_tel"><br>

    

    <label for="signup_pass_1">Jelszó</label><br>
    <input type="password" id="signup_pass_1"><br>
    <label for="signup_pass_2">Jelszó újra</label><br>
    <input type="password" id="signup_pass_2"><br>

    
</form>
<div id="please_be_centered">
    <button onclick="location.href='index.php'" id="signup_button">Regisztráció</button><br>
    <button onclick="location.href='login.php'" id="to_login_button">Belépés</button></div>
</div>