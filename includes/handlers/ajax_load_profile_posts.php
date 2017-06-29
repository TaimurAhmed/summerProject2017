<?php  
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Post.php");

/*Number of posts to be loaded in newfeed per call*/
$limit = 10; 

/*_REQUEST came via the Ajax call i.e. data*/
$posts = new Post($con, $_REQUEST['userLoggedIn']);
$posts->loadProfilePostFriends($_REQUEST, $limit);
?>

