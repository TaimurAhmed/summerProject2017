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
    </div>



    <div class="main_column column">
        This is a sample profile page
        <?php echo $user_profile_array['profile_pic']; ?>
        
    </div>



</div>
</body>
</html>
