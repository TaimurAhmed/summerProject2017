<?php 
require './config/config.php';
include("./includes/classes/User.php");
include("./includes/classes/Post.php"); 
include("./includes/classes/Message.php");
include("./includes/classes/Notification.php");

/*Regenerate cookies */
session_regenerate_id(true); //True parameter to enhance security i.e. delete session cookie from tmp
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
   <title>Artemis</title>
   <!--J Qeury-->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
   <!--JS-->
   <script src="./assets/bootstrap-3.3.7-dist/js/bootstrap.js"></script>
   <script src="./assets/js/timsi.js"></script>
   <script src="./assets/js/jcrop_bits.js"></script> 
   <script src="./assets/js/jquery.Jcrop.js"></script>
   <script src='https://www.google.com/recaptcha/api.js'></script>
   <!--CSS-->
   <link rel=stylesheet type="text/css" href="./assets/bootstrap-3.3.7-dist/css/bootstrap.css"></link>
   <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
   <script src="./assets/css/jquery.Jcrop.css"></script> 
   <!--CSS: Font Awesome CDN-->
   <script src="https://use.fontawesome.com/342380e526.js"></script>
   <script src="./assets/js/bootbox.min.js"></script>
   <!--Favicon of UoB logo used purely for demonstration, please replace if deploying-->
   <link rel="icon" href="./assets/favicon/logo.jpg" type="image/ico">
</head>

<body>
<?php
include './selfXSSwarning.php';
?>

<div class="top_bar">

      <!--Logo-->
      <div class="logo">
          <a role="link" id='HomePagelogo' aria-labelledby='HomePagelogo' aria-describedby='linkToHomepage' title="Artemis Homepage" href="index.php"> Artemis</a>
      </div>


      <!-- Search Bar-->
      <div class="search" role='search'>
        
        <form action="search.php" method="GET" name="search_form">
          <input id="search_text_input" aria-labelledby='search search_text_input' aria-describedby='searchQueryInput' type="search" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off">
          <div role='button' id='searchButton' aria-labelledby='search searchButton' aria-describedby='searchForQuery' class="button_holder" title="Search for other users">
            <i class="fa fa-search" aria-hidden="true"></i>
          </div>
        </form>
          
          <!--Live ARIA region:Polite-->
          <div id='searchBarResults' aria-labelledby='searchBarResults' aria-describedby='searchResultsList' aria-live='polite' role='list' aria-relevant="additions removals" class="search_results">
          </div>

          <div class="search_results_footer_empty">
          </div>
      </div>


        
        
        <!--Horizontal Top Navigation Bar-->
        <!--Explicit and Implicit Nav Bar WAI-ARIA role declaration-->
        <nav id='upper_right_nav_bar' role="navigation" aria-labelledby='upper_right_nav_bar' aria-describedby='navBar' >
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
            <a id=header_loggedInUser_name title="Personal Wall" role="link" aria-labelledby='header_loggedInUser_name' aria-describedby='linkToPersonalWall' href='<?php echo $userLoggedIn; ?>'>
                <?php 
                    if(isset($meta_person["first_name"])){
                        echo $meta_person["first_name"];
                    }
                ?>                       
            </a>
          

          <!-- Fiddle -->
          <a aria-label="fiddle.io" role="link" title="Code Fiddles" href='https://fiddles.io/' >
              <i class="fa fa-code" aria-hidden="true" >
                
              </i>
          </a>
            
          <!--Home Page i.e. New Feed-->
          <a aria-label="Newsfeed" role="link" title="Artemis Newsfeed" href="index.php">
              <i class="fa fa-home" aria-hidden="true">
              </i>
          </a>
            
            <!--Messages-->
            <a aria-label="Messages" role="link" title='Messages' href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')" >
                <i class="fa fa-envelope" aria-hidden="true"> 
                  <!--If there are any unopened messages give a notification-->
                  <?php 
                    echo 
                      ($num_messages) 
                      ? "<div role='alert' aria-relevant='all' role='number of unread messages' title='".$num_messages." unread message' <span class='notification_badge' id='unread_message'>". $num_messages ."</span></div>" 
                      : "";
                  ?>
                  </i>
            </a>
            
            <!--Notifications-->
            <a aria-label="Notifications" role="link" title='Notifications'   href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
                <i class="fa fa-bell" aria-hidden="true">
                  <?php 
                    echo 
                      ($num_notifications) 
                      ? "<div role='alert' aria-relevant='all' role='number of unread notifications' title='".$num_notifications." unread notification'><span class='notification_badge' id='unread_notification'>". $num_notifications ."</span></div>" 
                      : "";
                  ?>
                </i>
            </a>


            <!-- Friend Requests -->
            <a aria-label="friendRequests" role="link" title="Friend Requests" href="requests.php">
                <i class="fa fa-users" aria-hidden="true">
                  <?php 
                    echo 
                      ($num_requests) 
                      ? "<div role='alert' aria-relevant='all' role='number of unread requests' title='".$num_notifications." unchecked friend requests'><span class='notification_badge' id='unread_requests'>". $num_requests ."</span></div>" 
                      : "";
                  ?>
                </i>
            </a>
            
            <!--Settings Page-->
            <a aria-label="Settings" role="link" title='Profile Settings' href="settings.php"><i class="fa fa-cog" aria-hidden="true"></i></a>
            


            <!--Help Page-->
            <a aria-label="About" role="link" title='About' href="https://github.com/TaimurAhmed/summerProject2017/wiki" class="help_header_icon">
                <i class="fa fa-question-circle-o" aria-hidden="true"></i>
            </a>

            <!-- Sign Out-->
            <a aria-label="Sign Out" role="link" title='Sign Out' href="./includes/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
        
        </nav>
        
        <!--Live region which will 'politely' alert device of additions and removals...additions text is the default-->
        <div aria-live="polite" aria-relevant="additions removals" role='link' aria-label='notifications' title='Click Notification' class="dropdown_data_window">
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




