<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Movie</title>
        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
        <link href="./style.css", rel="stylesheet">
        <link href='https://fonts.googleapis.com/css2?family=Urbanist:wght@100&display=swap' rel='stylesheet'>
        <!-- <script src="/scripts/jquery.min.js"></script>
        <script src="/scripts/index.js"></script> -->
        <style>
            
        </style>

    </head>
    <body>
        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="./homepage.php">HOME</a>
                <form METHOD="GET" id="search-bar" style="margin-right: 5%;">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto" >
                            <input type="text" name="name" placeholder="Enter your movie name" >
                        </div>
                        <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Search</button>
                        </div>  
                    </div>
                </form>
            </div>
            <button type="button" class="btn btn-danger" style="width:5%;" onclick="logOut();">Log out</button>
        </nav>
        <div id="category-bar" style="text-align:center;">
            <?php
                echo "<div class=\"btn-group\" role=\"group\" aria-label=\"Basic example\">";
                foreach($categories as $value){
                    echo "<form action=\"./homepage.php\" METHOD=\"GET\">";
                    echo "<input type=\"hidden\" value=$value name=\"filter\" id=\"filter\">";
                    echo "<button type=\"submit\" class=\"btn btn-primary\">$value</button>";
                    echo "</form>";
                }
                echo "</div>";
            ?>
        </div>
        <div style="margin-left: 10px; margin-top: 10px; color: blue; font-size: 25px;">FILTER BY "<?php echo $filter; ?>"</div>
        <div class="container-fluid">
                <?php
                    $row = mysqli_fetch_assoc($result);
                    if(!$row){
                        echo "<span style=\"color:red\"> NOT FOUND! </span>";
                    }
                    else{
                        $count = 0;
                        do{
                            $count += 1;
                            if($count%5==1){
                                echo "<div class=\"row row-cols-1 row-cols-md-5 g-4\">";
                            }
                            echo displayCard($row);
                            if($count%5==0){
                                echo "</div>";
                            }   
                        } while($row = mysqli_fetch_assoc($result));
                    }
                ?>
        </div>
        <script>
            function logOut(){
                let result = window.confirm("Are you sure to log out?");
                if(result){
                    localStorage.removeItem('user_id');
                    window.location.replace("./login.php");
                }
            }
            var user_id = localStorage.getItem('user_id');
            if(user_id === null){
                window.location.replace("./login.php");
            }
            if(user_id === "1"){
                window.location.replace("./admin.php");
            }
        </script>
    </body>
<html>

