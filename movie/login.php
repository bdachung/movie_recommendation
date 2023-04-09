<?php
    include_once('connection.php');
    $err = "";
    $user = array(
        "user_id" => ""
    );
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        
        $username = $_POST["username"];
        $pwd = $_POST["pwd"];
        if(is_null($username) || is_null($pwd)){
            $err = "Username or password is invalid. Try again";
        }
        else{
            $query = "SELECT user_id FROM users WHERE user = '$username' AND pwd = '$pwd' ;" ;
            $result =  mysqli_query($link, $query);
            // if (! $result) {
            //     die ("error :)");
            //     die ($result);
            // }
            $user = mysqli_fetch_array($result);
            if(!$user){
                $err = "No username";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Movie</title>
        <link href="./style-login.css", rel="stylesheet">
        <link href='https://fonts.googleapis.com/css2?family=Urbanist:wght@100&display=swap' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <style>
            
        </style>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
    </head>
    <body>
        <!-- <p> test connect <?php //echo($link); ?> </p> -->
    

        <!-- <div id="login-form" class="modal animate"> -->
        <div id="login-form" class="container-sm" style="max-width: 512px">
            <div class="d-flex justify-content-center">

                <form class="modal-content" method="POST">
                    <div class="img-container">
                        <img class="img-avatar" src="https://bizweb.dktcdn.net/100/411/628/products/e4lta9zxwasgl1v-1-f22f4972-ede5-4ed3-89a0-41de09aa6f1e.jpg">
                    </div>
                    <div class="error" style="color:red; text-align: center;"> <?php echo $err; ?> </div>
                    <!-- <div class="container"> -->
                    <div class="">
                        <label for="username">Username</label>
                        <input type="text" name="username" placeholder="username">
                        <label for="pwd">Password</label> 
                        <input type="password" name="pwd" placeholder="password">
                        <label>Remember me</label> 
                        <input type="checkbox" name="remember" checked="checked"> 
                        <button>Login</button> 
                    </div>
                    <div class="">
                    <!-- <div class="container"> -->
                        <span class="psw">
                            
                            <a href="#">Forgot Password?</a>
                        </span>
                        <a href="./registerpage.php" style="text-decoration: none;">
                            <span class="cancel-btn psw" style="background-color: blue; color: white; width: 100%; margin-top: 15px;text-align: center;">Register</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <script>
            var user = <?php echo json_encode($user); ?>;
            if(user !== null){
                if(user['user_id'] !== ""){
                    localStorage.setItem('user_id',user['user_id']);
                }
                var user_id = localStorage.getItem('user_id');
                if(user_id === "1"){
                    window.location.replace("./admin.php");
                }
                else if(user_id !== null){
                    window.location.replace("./homepage.php");
                }
            }
            
        </script>
        
    </body>

<html>