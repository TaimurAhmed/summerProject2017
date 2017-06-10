<?php 

require './config/config.php';
/*Redirect users who are not logged in*/
if(isset($_SESSION["username"])){
    $userLoggedIn = $_SESSION["username"]; 

}else{
    header("Location:register.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <title>TimsiFeed:Wall </title>
   <script src="./assets/js/bootstrap.js"></script>
   <link rel=stylesheet type="text/css" href="./assets/css/bootstrap.css"></script>
</head>

<body>
