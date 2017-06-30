<?php
include("./includes/header.php");

$message_obj = new Message($con,$userLoggedIn);

//Username is already taken as value in the assoc array
if(isset($_GET['u'])){
    $user_to = $_GET['u'];
}else{
    //Assuming user has not messaged anyone yet
    $user_to = $message_obj->getMostRecentUser();
    if($user_to === false){
        $user_to = 'new';
    }
}

if($user_to != "new"){
    $user_to_obj = new User($con,$user_to); 
}

?>