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
            if (! $result) {
                die ("error :)");
            }
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
        <link href="./style.css", rel="stylesheet">
        <link href='https://fonts.googleapis.com/css2?family=Urbanist:wght@100&display=swap' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <style>
            
        </style>

    </head>
    <body>
        <!-- <p> test connect <?php //echo($link); ?> </p> -->

        <div id="login-form" class="modal animate">
            <form class="modal-content" method="POST">
                <div class="img-container">
                    <img class="img-avatar" src="./images/img_avatar.png">
                </div>
                <div class="error" style="color:red; text-align: center;"> <?php echo $err; ?> </div>
                <div class="container">
                    <label for="username">Username</label>
                    <input type="text" name="username" placeholder="username">
                    <label for="pwd">Password</label> 
                    <input type="password" name="pwd" placeholder="password">
                    <button>Login</button> 
                    <label>Remember me</label> 
                    <input type="checkbox" name="remember" checked="checked"> 
                </div>
                <div class="container">
                    <a href="./registerpage.php" style="text-decoration: none;">
                        <span class="cancel-btn" style="background-color: blue; color: white;">Register</span>
                    </a>
                    <span class="psw">
                        Forgot
                        <a href="#">Password?</a>
                    </span>
                </div>
            </form>
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