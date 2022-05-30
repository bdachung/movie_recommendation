<?php
    $categories = array('Action', 'Adventure',
    'Animation', 'Children_s', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy',
    'Film_Noir', 'Horror', 'Musical', 'Mystery', 'Romance', 'Sci_Fi', 'Thriller', 'War', 'Western');
    include('connection.php');
    header('Access-Control-Allow-Origin: *');
    if($_GET['name']){
        $name = $_GET['name'];
        $query = "SELECT movie_id, movie_title FROM items WHERE UPPER(movie_title) LIKE UPPER('%$name%') ;";
        $result =  mysqli_query($link, $query);
        include('searchbyname.php');
    }
    else if($_GET['filter']){
        $filter = $_GET['filter'];
        $query = "SELECT items.movie_id, movie_title FROM items, item_type WHERE items.movie_id = item_type.movie_id AND item_type.movie_type = \"$filter\" ;";
        $result =  mysqli_query($link, $query);
        include('filterbycategory.php');
    }
    else{
        $t=time();
        $d = date("Y-m-d H:i:s",$t);
        $query = "SELECT movie_id, movie_title FROM items WHERE release_date IS NOT NULL AND TIMESTAMPDIFF(DAY, release_date, '$d') <= 14 ;";
        $result =  mysqli_query($link, $query);
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
                <form METHOD="GET" id="search-bar" style="margin-right: 5%;">
                    <div class="row g-3 align-items-center" >
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

        <div id="latest">
            <h5 style="color:blue; font-size:30px; margin-top:10px;">Latest movies</h5>
            <?php
                $row = mysqli_fetch_assoc($result);
                if($row){
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

        <div id="popularity">
            <h5 style="color:blue; font-size:30px; margin-top:10px;">Popular for you</h5>
        </div>

        <div id="similarity">
            <h5 style="color:green; font-size:30px; margin-top:10px;">Users similar with you watching these</h5>
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
                    
                    $("#popularity").append(cards);

                }
            };
            xmlhttp.open("POST", "<?php echo $AISERVER; ?>" + "/popularity");
            xmlhttp.setRequestHeader("Content-type", "application/json");
            xmlhttp.send(JSON.stringify({"user_id":user_id}));

            var xmlhttp2 = new XMLHttpRequest();
            xmlhttp2.onreadystatechange = function() {
                if (xmlhttp2.readyState == 4 && xmlhttp2.status == 200) {
                    let data = JSON.parse("{" + xmlhttp2.responseText.slice(1,-1).replaceAll('\\',"") + "}"); 
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
                    
                    $("#similarity").append(cards);

                }
            };
            xmlhttp2.open("POST", "<?php echo $AISERVER; ?>" + "/uu_rec");
            xmlhttp2.setRequestHeader("Content-type", "application/json");
            xmlhttp2.send(JSON.stringify({"user_id":user_id}));
        </script>
    </body>
<html>

<?php
    }
function displayCard($data){
    $id = $data['movie_id'];
    $name = $data['movie_title'];
    return 
    " 
    <div class=\"col\">
        <div class=\"card\" id=\"$id\" >
            <a href=\"./movie.php?id=$id&name=$name\">
                <img src=\"https://picsum.photos/200/300?random=$id\" class=\"card-img-top\" alt=\"Movie\">
            </a>
            <div class=\"card-body\">
                <h5 class=\"card-title\" style=\"height: 3em;\">$name</h5>
            </div>
        </div>
    </div>
    ";
}
?>