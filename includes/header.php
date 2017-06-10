<?php 

require './config/config.php';
/*Redirect users who are not logged in*/
if(isset($_SESSION["username"])){
    $userLoggedIn = $_SESSION["username"]; 
    echo("this should happen");

}else{
    echo("this shouldnt happen!!");
    header("Location:register.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <title>TimsiFeed:Wall </title>
</head>

<body>
iafhklashfklhaklsfhasklfhkla