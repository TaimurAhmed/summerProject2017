<?php

ob_start(); /*Turns on output buffering*/
session_start();

$time_zone = date_default_timezone_set("Europe/London");

$con = mysqli_connect("localhost","root","","social"); /*Connection Variable*/

if(mysqli_connect_errno()){
   echo "Connection failed: ". mysqli_connect_errno();
   exit();
}

?>