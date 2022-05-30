<?php
    $occupations = array("other","academic/educator","artist","clerical/admin","college/grad student","customer service","doctor/health care","executive/managerial","farmer","homemaker","K-12 student","lawyer","programmer","retired","sales/marketing","scientist","self-employed","technician/engineer","tradesman/craftsman","unemployed","writer");
?>


<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Movie</title>
        <link href="./style.css", rel="stylesheet">
        <link href='https://fonts.googleapis.com/css2?family=Urbanist:wght@100&display=swap' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

    </head>
    <body>
        <div class="modal-content" method="POST" id="register-form" style="background-color: pink;">
            <h2 style="color: blue;">Register</h2>
                <div class="container">
                    <label for="username" >Username</label>
                    <input type="text" name="username" id="username" placeholder="username" required>
                    <label for="pwd">Password</label> 
                    <input type="password" name="pwd" id="pwd" placeholder="password" required)>
                    </br></br>
                    <label for="age">Age</label> 
                    <select name="age" id="age">
                        <option value=1>Under 18</option>
                        <option value=18>18-24</option>
                        <option value=25>25-34</option>
                        <option value=35>35-44</option>
                        <option value=45>45-49</option>
                        <option value=50>50-55</option>
                        <option value=56>56+</option>
                    </select> 
                    </br></br>
                    <span>Sex</span>
                    <input type="radio" name="sex" value="M" checked>
                    <label for="sex">Male</label>
                    <input type="radio" name="sex" value="F">
                    <label for="sex">Female</label>
                    </br></br>
                    <label for="occupation">Occupation</label> 
                    <select name="occupation" id="occupation">
                        <?php 
                            foreach ($occupations as $idx => $val){
                                echo "<option value=$idx>$val</option>";
                            }
                        ?>
                    </select> 
                        </br>
                    <button style="border-radius: 10px; background-color: green; width: 20%;" onclick="sendRegisterForm();">Register</button> 
                </div>
                        </div>
        <script>
            var user_id = localStorage.getItem('user_id');
            if(user_id === "1"){
                window.location.replace("./admin.php");
            }
            else if(user_id !== null){
                window.location.replace("./homepage.php");
            }
            function sendRegisterForm(){
                let user = $('#username').val();
                let pwd = $('#pwd').val();
                let sex = '';
                if($('input[name="sex"]')[0].checked){
                    sex = 'M';
                }
                else{
                    sex = 'F';
                }
                let age = parseInt($('#age').val());
                let occu = parseInt($('#occupation').val());
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        window.alert(xmlhttp.responseText);
                    }
                };
                xmlhttp.open("POST", "register.php");
                xmlhttp.setRequestHeader("Content-type", "application/json");
                xmlhttp.send(JSON.stringify({"age":age,"sex":sex,"occupation":occu,"user":user,"pwd":pwd}));
            }  
        </script>
    </body>
<html>
