<?php
    include('connection.php');
    header('Access-Control-Allow-Origin:*');
    $_POST = json_decode(file_get_contents('php://input'), true);
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $username = $_POST["user"];
        $query = "SELECT user_id FROM users WHERE user = '$username' ;" ;
        $result =  mysqli_query($link, $query);
        $user = mysqli_fetch_array($result);
        if($user){
            echo "The user has already existed!";
        }
        else{
            $pwd = $_POST["pwd"];
            $age = $_POST["age"];
            $sex = $_POST["sex"];
            $occu = $_POST["occupation"];
            $query = "SELECT MAX(user_id) from users ;";
            $result =  mysqli_query($link, $query);
            $user_id = intval(mysqli_fetch_array($result)[0]) + 1;
            $query = "INSERT INTO users (user_id,age,sex,occupation,user,pwd) VALUES ($user_id,$age,'$sex',$occu,'$username','$pwd') ;";
            $result =  mysqli_query($link, $query);
            if($result){
                echo "Create user successfully <3.";
            }
            else{
                echo "Failed to create user. Please try again.";
            }
        }
    }  
    mysqli_close($link);
?>