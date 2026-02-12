<!--I DONT KNOW-->
<?php include 'header.php'?>
<style>
    .container {height: 100%;width: 100%;margin-left: 1%!important;}
</style>

<body onload="generateContentForDisplay()">

<h1>Órarend  </h1> 
<div class="container">
    <div class="row rowcols-2 g-2">
        <div class="col-10">
            <div class="box-long-nb">
                <div class="row g-0">
                    <div class="col"><div class="box-long">
                        <p class="h6">Hétfő<hr></p>
                    </div></div>
                    <div class="col"><div class="box-long">
                        <p class="h6">Kedd<hr></p>
                    </div></div>
                    <div class="col"><div class="box-long">
                        <p class="h6">Szerda<hr></p>
                    </div></div>
                    <div class="col"><div class="box-long">
                        <p class="h6">Csütörtök<hr></p>
                    </div></div>
                    <div class="col"><div class="box-long">
                        <p class="h6">Péntek<hr></p>
                    </div></div>
                </div>
        </div></div>
        <div class="col-2">
            <div class="box-long-nb">
            <div class="box-smallest" style="margin-bottom:0.8em">
                <p class="h6">Osztály: <span id="osztaly_td"></span><hr></p>
                <p class="h6">Nyelv: <span id="nyelv_td"></span><hr></p>
                <p class="h6">Csoport: <span id="csoport_td"></span></p>
            </div>
            <select name="sometext" size="22" class="listbox" id="classes_" onchange="loadTimetable(this)"></select>
        </div></div>
    </div>




</div>
<br><button onclick="location.href='index.php'" id="home_button">home</button>
<button onclick="location.href='profile.php'" id="user_button">Felhasználó</button>
</body>

<script src="js/time_display.js"></script>