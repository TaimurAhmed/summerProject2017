<?php 

require './config/config.php';
/*Redirect users who are not logged in*/
if(isset($_SESSION["username"])){
    $userLoggedIn = $_SESSION["username"]; 

}else{
    header("Location:register.php");
}

require './includes/header_handler.php';


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
   <link rel=stylesheet type="text/css" href="./assets/bootstrap-3.3.7-dist/css/bootstrap.css"></link>
   <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
   <!--CSS: Font Awesome CDN-->
   <script src="https://use.fontawesome.com/342380e526.js"></script>
</head>

<body>

    <div class="top_bar">
        
        <div class="logo">
            <a href="index.php"> TimsiFeed</a>
        </div>
        
        <nav>
            <a href="#"><?php echo $first_name?></a>
            <a href="#"><i class="fa fa-home"></i></a>
            <a href="#"><i class="fa fa-envelope"></i></a>
            <a href="#"><i class="fa fa-bell-o"></i></a>
            <a href="#"><i class="fa fa-users"></i></a>
            <a href="#"><i class="fa fa-cog"></i></a>


        </nav>
    
    </div>




