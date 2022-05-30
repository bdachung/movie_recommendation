function sendToAI(user_id){
    console.log(user_id)
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            window.alert(xmlhttp.responseText);        
        }
    };
    xmlhttp.open("POST", "http://127.0.0.1:8000/popularity");
    xmlhttp.setRequestHeader("Content-type", "application/json");
    xmlhttp.send(JSON.stringify({"user_id":user_id}));
}