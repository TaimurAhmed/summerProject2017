<?php 

require './config/config.php';
include("./includes/classes/User.php");
include("./includes/classes/Post.php"); 
include("./includes/classes/Message.php");
include("./includes/classes/Notification.php");

/*Redirect users who are not logged in*/
if(isset($_SESSION["username"])){
    $userLoggedIn = $_SESSION["username"];
    $userMeta = array();
    $getUserMeta = "SELECT username,profile_pic,first_name,last_name,email FROM users WHERE username = ?";
    if($stmt = mysqli_prepare($con,$getUserMeta)){
          mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt,$userMeta['username'],$userMeta['profile_pic'],$userMeta['first_name'],$userMeta['last_name'],$userMeta['email']);
          mysqli_stmt_fetch($stmt);
          mysqli_stmt_close($stmt);
    }else{
      header("Location:register.php");
    }


}else{
    header("Location:register.php");
}

require './includes/header_handler.php';


?>

<html lang="en">

<head>
   <meta charset="UTF-8">
   <title>TimsiFeed:Wall </title>
   <!--J Qeury-->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
   <!--JS-->
   <script src="./assets/bootstrap-3.3.7-dist/js/bootstrap.js"></script>
   <script src="./assets/js/timsi.js"></script>
   <script src="./assets/js/jcrop_bits.js"></script> 
   <script src="./assets/js/jquery.Jcrop.js"></script> 

   <!--CSS-->
   <link rel=stylesheet type="text/css" href="./assets/bootstrap-3.3.7-dist/css/bootstrap.css"></link>
   <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
   <script src="./assets/css/jquery.Jcrop.css"></script> 
   <!--CSS: Font Awesome CDN-->
   <script src="https://use.fontawesome.com/342380e526.js"></script>
   <script src="./assets/js/bootbox.min.js"></script>
</head>

<body>

<div class="top_bar">

<img src="" alt="">
<!--Logo-->
<div class="logo">
    <a href="index.php"> TimsiFeed</a>
</div>


<!-- Search Bar-->
<div class="search">

  <form action="search.php" method="GET" name="search_form">
    
    <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">

    <div class="button_holder">
      <i class="fa fa-search" aria-hidden="true"></i>
    </div>

  </form>

      <div class="search_results">
      </div>

      <div class="search_results_footer_empty">
      </div>



</div>



        
        
        <!--Horizontal Top Navigation Bar-->
        <nav>
          <?php

            /*Unread messages*/
            $messages = new Message($con,$userLoggedIn);
            $num_messages = $messages->getUnreadNumber();

            /*Unread notifications*/
            $notifications = new Notification($con,$userLoggedIn);
            $num_notifications = $notifications->getUnreadNumber();

            /* friend requests */
            $user_obj = new User($con,$userLoggedIn);
            $num_requests = $user_obj->getNumOfFriendRequests();
          ?>
            <a href="#">
                <?php 
                    if(isset($meta_person["first_name"])){
                        echo $meta_person["first_name"];
                    }
                ?>                       
            </a>
            <!--Home Page-->
            <a href="index.php">
                <i class="fa fa-home">
                </i>
            </a>
            <!--Messages-->
            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
                <i class="fa fa-envelope"> 
                  <!--If there are any unopened messages give a notification-->
                  <?php 
                    echo 
                      ($num_messages) 
                      ? "<span class='notification_badge' id='unread_message'>". $num_messages ."</span>" 
                      : "";
                  ?>
                  </i>
            </a>
            <!--Notifications-->
            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
                <i class="fa fa-bell">
                  <?php 
                    echo 
                      ($num_notifications) 
                      ? "<span class='notification_badge' id='unread_notification'>". $num_notifications ."</span>" 
                      : "";
                  ?>
                </i>
            </a>

            <a href="requests.php">
                <i class="fa fa-users">
                  <?php 
                    echo 
                      ($num_requests) 
                      ? "<span class='notification_badge' id='unread_requests'>". $num_requests ."</span>" 
                      : "";
                  ?>
                </i>
            </a>
            <a href="settings.php"><i class="fa fa-cog"></i></a>
            <a href="./includes/handlers/logout.php"><i class="fa fa-sign-out"></i></a>
        </nav>
        
        <div class="dropdown_data_window">
          <input type="hidden" id="drop_down_data_type" value="">
        </div>
    </div>

    <!--Infinite Scrolling for messages-->
    <script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';

    $(document).ready(function() {
 

          $('.dropdown_data_window').scroll(function() {
              var inner_height = $('.dropdown_data_window').innerHeight(); /*Div containing msg data */
              var scroll_top = $('.dropdown_data_window').scrollTop();
              var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();/*Sets hidden inputs field*/ 
              var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();/*No more drop down msg data*/

              /*If no more posts via Post class is set to true, do no execute*/ 
              if ((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') {
                
                var pageName; /*Holds name of page to send ajax request to*/
                var type =  $('#dropdown_data_type').val();
                /*Input tag will get value*/
                if(type == 'notification'){
                  pageName = "ajax_load_notifications.php"; //TBA
                }else{ 
                  //if(type == "message"){
                    pageName = "ajax_load_messages.php";//Done
                  //}
                }


                var ajaxReq = $.ajax({
                    url: "includes/handlers/"+pageName,
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                    cache:false,

                    success: function(response) {
                        $('.dropdown_data_window').find('.nextPageDropdownData').remove(); /*Removes current .nextpage */
                        $('.dropdown_data_window').find('.noMoreDropdownData').remove(); 
                        $('.dropdown_data_window').append(response);
                    }
                });

              } //End if statement

              return false;

          }); //End (window).scroll(function())


      });

  </script>
<!--To prevent message form from scrolling to top when new message is sent--> 
  <script>
      var div = document.getElementById("scroll_messages");
      div.scrollTop = div.scrollHeight;
  </script>

    <div class="wrapper">




