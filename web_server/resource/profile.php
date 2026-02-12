<?php include 'header.php'?>
<style>
    .container{margin-top: 5%;};
   
</style>

<body onload="loadUserData()">
<div class="container" >

    <div class="col-6">
        <div class="box-medium">
            <div class="row rowcols-2 g-0">
                <div class="col-4">
                    <div class="col"><div class="box-smallest-nb" id="pfp"></div></div>
                    <div class="col"><div class="box-tiny-nb">
                        Profilkép:
                        <button class="change">[Megváltoztatás]</button>
                    </div></div>
                </div>
                <div class="col-8">
                    <div class="col"><div class="box-small" >
                        <b>Felhasználónév:</b><br> 
                        &nbsp;&nbsp;&nbsp;&nbsp;  <span id="og_display"></span><br>
                        <button class="change">[Megváltoztatás]</button><br>
                        <b>Email:</b><br> 
                        &nbsp;&nbsp;&nbsp;&nbsp; <span id="mail_add"></span><br>
                        <b>Jelszó: </b><br> 
                        <button class="change">[Megváltoztatás]</button><br>
                        <b>Telefonszám:</b> <br> 
                        &nbsp;&nbsp;&nbsp;&nbsp; <span id="og_tel"></span><br>
                        <button class="change">[Megváltoztatás]</button><br>
                        
                    </div></div>
                </div>
            
        
            
</div>
        </div>
    </div>
    
            
    <button onclick="location.href='login.php'" id="signout_button">Kilépés</button>
    
</div>   
<br><button onclick="location.href='index.php'" id="home_button">home</button>

<script src="js/profile.js"></script>
</body>