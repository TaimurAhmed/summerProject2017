 <?php 
require './includes/header.php';

if(isset($_GET['profile_username'])){
    $username = $_GET['profile_username'];
    $build_user_profile_query = "SELECT profile_pic FROM users WHERE username = ?";
    $user_profile_array = array();
        if($stmt = mysqli_prepare($con,$build_user_profile_query)){
            mysqli_stmt_bind_param($stmt, "s",$username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$user_profile_array['profile_pic']);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
}


?>

    <div class="profile_left">
        <img src="<?php echo $user_profile_array['profile_pic'];?>"/>
    </div>

    <div class="profile_info">
        
    </div>

    <div class="main_column column">
        This is a sample profile page
        <?php echo $user_profile_array['profile_pic']; ?>
        
    </div>



</div>
</body>
</html>
