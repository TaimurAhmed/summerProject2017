 <?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Notification.php");


/*Number of messages to Load*/
$limit = 7;

$notification = new Notification($con, $_REQUEST['userLoggedIn']);//Request from Ajax call

echo $notification->getNotification($_REQUEST, $limit);

?>