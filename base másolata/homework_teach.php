<?php include 'stylesheet_call.php'?>
<?php //echo'<h1 style="font-size: 9em"><strong>✨13th reason✨</strong></h1>'?>


<style>
    table{width: 1250px;}
    
    table, td,tr{border: 3px yellowgreen solid; padding: 0;}
    .row5{background-color: blue; width: 50px; height: 500px;}
    .row3{background-color: green;width: 50px; height: 50px;}
    .row5col3{background-color: blue; height: 500px; width: 200px;}
    .col1{max-width: 60px; background-color: purple;}

</style>

<h1>Házi feladat(teacher)</h1>

<table>
    <tr>
        <td>tantárgy</td>
        <td colspan="4">határidő</td>
        <td>dolgozat</td>
    </tr>
    <tr>
        <td rowspan="4"><div class="col1" id="targy">a</div></td>
        <td colspan="4">
            <div class="row3" ></div>
        </td>
        
        <td rowspan="4" ><div class="col1">a</div></td>
    </tr>
    <tr>
        <td colspan="3">leírás</td>
        
        <td> leadás</td>
        
        
    </tr>
    <tr>
        <td colspan="3"> <div class="row5col3"></div></td>
        <td> <div class="row5"></div></td>
        
        
    </tr>
    
</table>

<br><button onclick="location.href='index.php'" id="home_button">home</button>
<button onclick="location.href='profile.php'" id="user_button">Felhasználó</button>