<?php

/*Turns on output buffering*/
ob_start(); 

/*Set: Cookie expiration/lifetime, cookie works on all domain paths, cookie domains, no SSL, http only i.e. to prevent JS injection*/
/*Reccomended: Consider using an SSL protocol to prevent session hijacking. In that case set parameter to true instead*/
session_set_cookie_params(time()+30,'/','localhost',false,true);
session_start();

$time_zone = date_default_timezone_set("Europe/London");

$con = mysqli_connect("localhost","root","","social"); /*Connection Variable*/

if(mysqli_connect_errno()){
   echo "Connection failed: ". mysqli_connect_errno();
   exit();
}

?>