<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    include('connection.php');
    header('Access-Control-Allow-Origin:*');
    $_POST = json_decode(file_get_contents('php://input'), true);
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $movie_title = $_POST["movie_title"];
        $movie_types = $_POST["movie_types"];
        $movie_types = explode('|',$movie_types);

        $query = "SELECT MAX(movie_id) FROM items ;" ;
        $result =  mysqli_query($link, $query);
        $movie_id = intval(mysqli_fetch_array($result)[0]) + 1;
        $t=time();
        $d = date("Y-m-d H:i:s",$t);
        $query = "INSERT INTO items(movie_id, movie_title, release_date) VALUES ($movie_id,'$movie_title','$d') ;";

        $result =  mysqli_query($link, $query);
        if(!$result){
            echo "Failed to add movie !o!";
        }
        else{
            $flag = True;
            foreach($movie_types as $movie_type){
                $query = "INSERT INTO item_type(movie_id,movie_type) VALUES ($movie_id,'$movie_type') ;";
                $result = mysqli_query($link, $query);
                $flag = $flag && ($result ? True : False);
            }
            if($flag){
                echo "Add new movie successfully ~~";
            }else{
                $query = "DELETE FROM item_type WHERE movie_id = $movie_id ;";
                echo "Failed to add movie !o!";
            }
        }
    }  
    mysqli_close($link);
?>
