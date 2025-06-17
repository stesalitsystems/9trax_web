<?php
$my_apikey = "GV9XBTZIV15NYIQ8EHNJ"; 
$destination = "8017385265";
$message = "hello world"; 
$api_url = "http://panel.apiwha.com/send_message.php"; 
$api_url .= "?apikey=". urlencode ($my_apikey); 
$api_url .= "&number=". urlencode ($destination); 
$api_url .= "&text=". urlencode ($message); 
$my_result_object = json_decode(file_get_contents($api_url, false)); 
var_dump($my_result_object);
?>