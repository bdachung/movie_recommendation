<?php
    include('connection.php');
    header('Access-Control-Allow-Origin: *');
    $categories = array('Action', 'Adventure',
    'Animation', 'Children_s', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy',
    'Film_Noir', 'Horror', 'Musical', 'Mystery', 'Romance', 'Sci_Fi', 'Thriller', 'War', 'Western');
?>

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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <style>
            
        </style>

    </head>
    <body>
        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="./admin.php">ADMIN</a>
            </div>
            <button type="button" class="btn btn-danger" style="width:5%;" onclick="logOut();">Log out</button>
            
        </nav>
        
        <div style="text-align: center; margin-top: 10%; ">
            <input type="number" name="kuser" id="kuser" placeholder="Enter K similar users" style="width:30%;">
            <button type="button" class="btn btn-primary" style="width:10%;" onclick="retrainModel(kuser.value);">Retrain model</button>
            </br>
            <input type="number" name="kpop" id="kpop" placeholder="Enter K popularity-based movies want to show users" style="width:30%;">
            <button type="button" class="btn btn-primary" style="width:10%;" onclick="updateKPOP(kpop.value);">Update</button>
            </br>
            <input type="number" name="ksim" id="ksim" placeholder="Enter K similarity-based movies want to show users" style="width:30%;">
            <button type="button" class="btn btn-primary" style="width:10%;" onclick="updateKSIM(ksim.value);">Update</button>
            </br>
            <input type="text" id="movie_title" name="movie_title" style="width:30%;" placeholder="Enter movie title"> 
            <button type="button" class="btn btn-primary" id="addMovieForm" onclick="addMovie(movie_title.value);" style="width:10%;">Add movie</button>
            </br>
            <?php
                foreach($categories as $idx => $value){
                    if($idx%6 == 0){
                        echo "</br>";
                    }
                    echo "<span class=\"category\">"; 
                    echo "<input type=\"checkbox\" id=\"kind\" value=\"$value\">";
                    echo "<label for=\"kind\" style=\"margin-right: 10px; margin-left: 5px; color:black; width: 6%; text-align:left;\"> $value </label>";
                    echo "</span>";
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
            function retrainModel(value){
                let result = window.confirm("Are you sure to update retrain model ?");
                if(result){
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                    if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        window.alert(xmlhttp.responseText);
                    }
                    };
                    xmlhttp.open("POST", "<?php echo $AISERVER; ?>" + "/retraining");
                    xmlhttp.setRequestHeader("Content-type", "application/json");
                    xmlhttp.send(JSON.stringify({"num":value}));
                }
            }
            function updateKPOP(value){
                let result = window.confirm("Are you sure to update K POPULARITY-based movie ?");
                if(result){
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                    if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        window.alert(xmlhttp.responseText);
                    }
                    };
                    xmlhttp.open("POST", "<?php echo $AISERVER; ?>" + "/updateKPOP");
                    xmlhttp.setRequestHeader("Content-type", "application/json");
                    xmlhttp.send(JSON.stringify({"num":value}));
                }
            }
            function updateKSIM(value){
                let result = window.confirm("Are you sure to update K SIMILARITY-based movie ?");
                if(result){
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                    if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        window.alert(xmlhttp.responseText);
                    }
                    };
                    xmlhttp.open("POST", "<?php echo $AISERVER; ?>" + "/updateKCOLLAB");
                    xmlhttp.setRequestHeader("Content-type", "application/json");
                    xmlhttp.send(JSON.stringify({"num":value}));
                }
            }
            function addMovie(value){
                let result = window.confirm("Are you sure to add this movie ?");
                if(result){
                    if(value.length < 3){
                        window.alert("The movie title is too short. Please enter new title");
                        return ;
                    }

                    var movie_types = "";
                    $("#kind:checked").each(function(idx){
                        if(idx !== 0){
                            movie_types += '|' + this.value;
                        }
                        else{
                            movie_types += this.value;
                        }
                    })
                    if(movie_types === ""){
                        window.alert("No types are chosen. Please choose one.");
                        return;
                    }
                    
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                    if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        window.alert(xmlhttp.responseText);
                    }
                    };
                    xmlhttp.open("POST", "./addMovie.php");
                    xmlhttp.setRequestHeader("Content-type", "application/json");
                    xmlhttp.send(JSON.stringify({"movie_title":value,"movie_types":movie_types}));
                }
            }
        </script>
    </body>
<html>

