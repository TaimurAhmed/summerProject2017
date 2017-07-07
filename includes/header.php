<?php 

require './config/config.php';
include("./includes/classes/User.php");
include("./includes/classes/Post.php"); 
include("./includes/classes/Message.php");

/*Redirect users who are not logged in*/
if(isset($_SESSION["username"])){
    $userLoggedIn = $_SESSION["username"]; 

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
        
        <div class="logo">
            <a href="index.php"> TimsiFeed</a>
        </div>
        
        <!--Horizontal Top Navigation Bar-->
        <nav>
            <a href="#">
                <?php 
                    if(isset($meta_person["first_name"])){
                        echo $meta_person["first_name"];
                    }
                ?>                       
            </a>
            <a href="index.php">
                <i class="fa fa-home"></i>
            </a>
      <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
        <i class="fa fa-envelope"></i>
      </a>
            <a href="#"><i class="fa fa-bell-o"></i></a>
            <a href="requests.php"><i class="fa fa-users"></i></a>
            <a href="#"><i class="fa fa-cog"></i></a>
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
          /*Show the newsfeed loading symbol*/
          $('#loading').show();

          /*Ajax request for more posts on news feeds*/ 
          $.ajax({
              url: "./includes/handlers/ajax_load_posts.php",
              type: "POST",
              data: "page=1&userLoggedIn=" + userLoggedIn,
              cache:false,

              success: function(data) {
                  $('loading').hide();
                  $('.posts_area').html(data);
              }
          });

          $(window).scroll(function() {
              var height = $('.posts_area').height(); //Height of div containing posts
              var scroll_top = $(this).scrollTop();
              var page = $('.posts_area').find('.nextPage').val();/*Sets hidden inputs field*/ 
              var noMorePosts = $('.posts_area').find('.noMorePosts').val();

              /*If no more posts via Post class is set to true, do no execute*/ 
              if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                  
                  $('#loading').show();
                  //alert("Test: I am being called");

                  var ajaxReq = $.ajax({
                      url: "./includes/handlers/ajax_load_posts.php",
                      type: "POST",
                      data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                      cache:false,

                      success: function(response) {
                          $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
                          $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

                          $('#loading').hide();
                          $('.posts_area').append(response);
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




