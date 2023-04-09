<?php
    $username = "root";
    $password = "luan";
    $host = "localhost";
    $database = "recommendation";
    $link = mysqli_connect($host,$username,$password);
    if( ! $link  ) {
        die('No connection: ' . mysqli_connect_error());
        die('Could not connect: '.mysqli_error($link));
    }
    if(!$link){

    // echo ("<p>
        
    //     var_dump(function_exists('mysqli_connect'))
    //     </p>"
    // );

    }
    // $selected = mysqli_select_db($link, $database) or die("Could not select ".$database);
    $selected = mysqli_select_db($link, $database);
    // $selected = mysqli_select_db($link, $database) or var_dump(function_exists('mysqli_connect'));
    // echo (<p>
    // mysqli_select_db($link, $database)
    // </p>)    
    $AISERVER = "http://127.0.0.1:8000"
?>