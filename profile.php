 <?php 
require './includes/header.php';
$message_obj =  new Message($con,$userLoggedIn) ;


if(isset($_GET['profile_username'])){
    $username = $_GET['profile_username'];
    $build_user_profile_query = "SELECT profile_pic,friend_array,num_posts,num_likes FROM users WHERE username = ?";
    $user_profile_array = array();
        if($stmt = mysqli_prepare($con,$build_user_profile_query)){
            mysqli_stmt_bind_param($stmt, "s",$username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$user_profile_array['profile_pic'],$user_profile_array['friend_array'],$user_profile_array['num_posts'],$user_profile_array['num_likes']);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
    /*Count number of friends*/
    $extraComma = 1;
    $num_friends = substr_count($user_profile_array['friend_array'], ",") -  $extraComma ; 
}

/*to call remove friend*/
if(isset($_POST['remove_friend'])){
    $user = new User($con,$userLoggedIn);
    $user->removeFriend($username);
}

/*to send friend request*/
if(isset($_POST['add_friend'])){
    $user = new User($con,$userLoggedIn);
    $user->sendRequest($username);
}


/*Redirect to respond friend request*/
if(isset($_POST['respond_request'])){
    header("Location: requests.phps");
}

/**/
if(isset($_POST['post_message'])){
    if(isset($_POST['message_body'])){
        $body  = mysqli_real_escape_string($con,$_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($username,$body,$date);
    }

  $link = '#profileTabs a[href="#messages_div"]';/*Otherwise there is a php error due to speech marks*/
  /*To prevent changing tabs when messages is sent*/
  echo "<script> 
          $(function() {
              $('" . $link ."').tab('show');
          });
        </script>";
}


?>
    <style type="text/css">
        .wrapper{
            margin-left: 0px;
            padding-left: 0px;
        }
    </style>

    <div class="profile_left">
        <img src="<?php echo $user_profile_array['profile_pic'];?>"/>
            <div class="profile_info ">
            <p><?php echo "Posts: ". $user_profile_array['num_posts'] ?></p>
            <p><?php echo "Likes: ". $user_profile_array['num_likes'] ?></p>
            <p><?php echo "Friends : ". $num_friends ?></p>
            </div>
        
        <form action="<?php echo $username; ?>" method="POST">
            <?php
                $profile_user_obj  = new User($con,$username);
                if($profile_user_obj->isClosed()){
                    header("Location: user_closed.php");
                }
            

            $logged_in_user_obj = new User($con,$userLoggedIn);
            if($userLoggedIn != $username) {

                if($logged_in_user_obj->isFriend($username)) {
                    echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';
                }
                else if ($logged_in_user_obj->didRecieveRequest($username)) {
                    echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
                }
                else if ($logged_in_user_obj->didSendRequest($username)) {
                    echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
                }
                else 
                    echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';

            }
            ?>
        </form>
        <input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post Something">
        
        <?php
            if($userLoggedIn != $username){
                echo "<div class='profile_info_bottom'>";
                echo "Mutual Friends: " . $logged_in_user_obj->getMutualFriends($username);
                echo "</div>";
            }
        ?>



    </div>




    <div class="profile_main_column column">
        <!--Bootstrap Tabs-->
        <ul class="nav nav-tabs" role="tablist" id="profileTabs">
          <li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Home</a></li>
          <li role="presentation"><a href="#about_div" aria-controls="about_div " role="tab" data-toggle="tab">About</a></li>
          <li role="presentation"><a href="#messages_div" aria-controls="messages_div " role="tab" data-toggle="tab">Messages</a></li>
        </ul>

        <div class="tab-content">
                
                <!--NewsFeed Div-->
                <div role="tabpanel" class="tab-pane fade in active" id="newsfeed_div">

                    <!--Font Awesome: Loading/Inprocess Icon-->
                    <div class="posts_area"></div>
                    <div id='#loading'> 
                    <i class="fa fa-refresh fa-spin fa-3x fa-fw" ></i>
                    </div>

                </div>
                
                <!--About Div-->
                <div role="tabpanel" class="tab-pane fade" id="about_div">
                <?php
                    echo "Name : ". $logged_in_user_obj->getFirstAndLastName(). "<br><br><br>";
                    echo "User ID: " . $logged_in_user_obj->getUsername(). "<br>";
                ?>

                </div>

                <!--Messages  Div-->
                <div role="tabpanel" class="tab-pane fade" id="messages_div"> 
                    <!--Candidate for(messages) abstracting-->
                    <?php
                                echo "<h4>You and <a href='" . $username ."'>" . $profile_user_obj->getFirstAndLastName() . "</a></h4><hr><br>";
                                echo "<div class='loaded_messages' id='scroll_messages'>";
                                echo $message_obj->getMessages($username);/*Loaded requested messages*/
                                echo "</div>";
                            ?>

                            <div class="message_post">
                                <form action="" method="POST">
                                        <textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>
                                        <input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
                                </form>

                            </div>


                </div>


        </div>

    

    </div>





<!-- Modal -->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="postModalLabel">Post something!</h4>
      </div>

      <div class="modal-body">
        <p>This will appear on the user's profile page and also their newsfeed for your friends to see!</p>

        <form class="profile_post" action="" method="POST">
            <div class="form-group">
                <textarea class="form-control" name="post_body"></textarea>
                <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
                <input type="hidden" name="user_to" value="<?php echo $username; ?>">
            </div>
        </form>
      </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
      </div>
    </div>
  </div>
</div>

<!--Load profile posts-->
        <script>
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
            var profileUsername = '<?php echo $username  ?>';

            $(document).ready(function() {
                /*Show the newsfeed loading symbol*/
                $('#loading').show();

                /*Ajax request for more posts on news feeds*/ 
                //For no posts loaded yet (as opposed to infinite scrolling )
                $.ajax({
                    url: "./includes/handlers/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername="+profileUsername,
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
                        //For infinite scrolling
                        var ajaxReq = $.ajax({
                            url: "./includes/handlers/ajax_load_profile_posts.php",
                            type: "POST",
                            data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
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




</div>
</body>
</html>
