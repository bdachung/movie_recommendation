<?php
    $username = "root";
    $password = "";
    $host = "localhost";
    $database = "recommendation";
    $link = mysqli_connect($host,$username,$password);
    if(!$link){
        die('Could not connect: '.mysqli_error($link));
    }
    $selected = mysqli_select_db($link, $database) or die("Could not select ".$database);
    $AISERVER = "http://127.0.0.1:8000"
?>