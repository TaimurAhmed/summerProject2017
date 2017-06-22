 <?php 
require './includes/header.php';


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
    </div>




    <div class="main_column column">
        This is a sample profile page
        <?php echo $user_profile_array['profile_pic']; ?>

 
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



</div>
</body>
</html>
