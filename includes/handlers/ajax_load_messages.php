<?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Message.php");


/*Number of messages to Load*/
$limit = 7;

$message = new Message($con, $_REQUEST['userLoggedIn']);//Request from Ajax call

echo $message->getConvosDropdown($_REQUEST, $limit);

?>