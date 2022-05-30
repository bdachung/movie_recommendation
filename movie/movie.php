<?php
    include('connection.php');
    header('Access-Control-Allow-Origin: *');
    $movie_id = $_GET['id'];
    $movie_title = $_GET['name'];
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
                <a class="navbar-brand" href="./homepage.php">HOME</a>
                <form METHOD="GET" action="./homepage.php" id="search-bar" style="margin-right: 5%;">
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
        <div style="text-align: center;">
            <img src="https://picsum.photos/1600/800?random=<?php echo $_GET['id'];?> class="card-img-top" alt="Movie">      
        </div>

        <div id="header" style="margin-left: 120px; margin-top:20px;">
            <h5 style="color: black; font-size: 30px;"><?php echo $_GET['name'];?></h5>
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-primary" style="border-radius: 50%; background-color:#00bfff;" onclick="rateMovie(parseInt(this.innerHTML));">1</button>
                <button type="button" class="btn btn-primary" style="border-radius: 50%; background-color:#00bfff;" onclick="rateMovie(parseInt(this.innerHTML));">2</button>
                <button type="button" class="btn btn-primary" style="border-radius: 50%; background-color:#00bfff;" onclick="rateMovie(parseInt(this.innerHTML));">3</button>
                <button type="button" class="btn btn-primary" style="border-radius: 50%; background-color:#00bfff;" onclick="rateMovie(parseInt(this.innerHTML));">4</button>
                <button type="button" class="btn btn-primary" style="border-radius: 50%; background-color:#00bfff;" onclick="rateMovie(parseInt(this.innerHTML));">5</button>
            </div>
        </div>

        <div id="knn">
            <h5 style="color:green; font-size:30px; margin-top:10px;">Same kinds of movies</h5>
        </div>


        <script>
            function logOut(){
                let result = window.confirm("Are you sure to log out?");
                if(result){
                    localStorage.removeItem('user_id');
                    window.location.replace("./login.php");
                }
            }
            function showCard(key, value){
                return `<div class="col"> <div class="card" id="` + String(key) + `" > <a href="./movie.php?id=` + String(key) + `&name=` + value + `"><img src="https://picsum.photos/200/300?random=` + String(key) + `" class="card-img-top" alt="Movie"></a> <div class="card-body"> <h5 class="card-title" style="height: 3em;">`+ value + `</h5> </div> </div> </div>`;
            }
            var user_id = localStorage.getItem('user_id');
            if(user_id === null){
                window.location.replace("./login.php");
            }

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    let data = JSON.parse("{" + xmlhttp.responseText.slice(1,-1).replaceAll('\\',"") + "}"); 
                    let count = 0;      
                    let cards = "";
                    for(const[key, value] of Object.entries(data)){
                        count += 1;
                        if(count%5 == 1){
                            cards += `<div class="row row-cols-1 row-cols-md-5 g-4">`;
                        }
                        
                        cards += showCard(key, value);
                        if(count%5 == 0){
                            cards += `</div>`;
                        }
                    }
                    if(count < 5) {
                        cards += `</div>`;
                    }
                    
                    $("#knn").append(cards);

                }
            };
            xmlhttp.open("POST", "<?php echo $AISERVER; ?>" + "/knn_rec");
            xmlhttp.setRequestHeader("Content-type", "application/json");
            xmlhttp.send(JSON.stringify({"movie_id":<?php echo $movie_id; ?>}));

            rateMovie(0);

            function rateMovie(rating){
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    if((xmlhttp.responseText >= "1") && (xmlhttp.responseText <= "5")){
                        $(".btn-group button:nth-child("+xmlhttp.responseText+")").css("background-color","#000080");
                    }
                    else if(xmlhttp.responseText !== "0"){
                        window.alert(xmlhttp.responseText);
                        for(let i = 1 ; i <= 5 ; i++){
                            if(i==rating){
                                $(".btn-group button:nth-child("+String(i)+")").css("background-color","#000080");
                            }
                            else{
                                $(".btn-group button:nth-child("+String(i)+")").css("background-color","#00bfff");
                            }
                        }
                    }        
                }
            };
                xmlhttp.open("POST", "<?php echo $AISERVER; ?>" + "/rate");
                xmlhttp.setRequestHeader("Content-type", "application/json");
                xmlhttp.send(JSON.stringify({"user_id":user_id,"movie_id":<?php echo $movie_id; ?>,"rating":rating}));
            }
            
        </script>
    </body>
<html>