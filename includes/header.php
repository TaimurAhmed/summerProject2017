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
   <!--JS-->
   <script src="./assets/bootstrap-3.3.7-dist/js/bootstrap.js"></script>
   <!--J Qeury-->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
   <!--CSS-->
   <link rel=stylesheet type="text/css" href="./assets/bootstrap-3.3.7-dist/css/bootstrap.css"></script>
</head>

<body>


<!-- Single button -->
<div class="btn-group">
  <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Action <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
    <li><a href="#">Action</a></li>
    <li><a href="#">Another action</a></li>
    <li><a href="#">Something else here</a></li>
    <li role="separator" class="divider"></li>
    <li><a href="#">Separated link</a></li>
  </ul>
</div>