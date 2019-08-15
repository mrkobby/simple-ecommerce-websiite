<?php  
$db_host = "localhost"; 
$db_username = "root";  
$db_pass = "";  
$db_name = "onlinestore"; 
$db_connection =  mysqli_connect("$db_host","$db_username","","$db_name");
if (mysqli_connect_errno()){
		echo mysqli_connect_error();
		exit();
}      
?>