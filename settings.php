<?php 
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");

?>

<div class="main_column column ">

    <h4> Account Settings</h4>
    
    <?php
        echo "<img src='" .$userMeta['profile_pic']. "'/>";
    ?>
    <br>
    <a href="upload.php">Upload New Profile Picture</a><br><br><br>

    Modify details and click  'Update Details'.
    


    <?php
    //To refresh page as well
    $user_data_query = "SELECT first_name,last_name, email FROM users WHERE username= ?";
    if($stmt = mysqli_prepare($con,$user_data_query)){
        mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$first_name,$last_name,$email);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }
    ?>


    <!--Non encryped user meta data-->
    <form action="settings.php" method="POST">
        First Name: <input type="text" name="first_name" value="<?php echo $first_name; ?>">
        <br>
        Last Name: <input type="text" name="last_name" value="<?php echo $last_name; ?>">
        <br>
        Email: <input type="text" name="email" value="<?php echo $email; ?>">
        <br>
        <input type="submit" name="update_details" id="save_details" value="Update Details">
        <br>
        <?php echo $message; ?>
    </form>

    <!--Encryped user meta data-->
    <h4>Change Password</h4>
    <form action="settings.php" method="POST">
        Old Password: <input type="password" name="old_password" >
        <br>
        New Password: <input type="password" name="new_password_1" >
        <br>
        Confirm New Password: <input type="password" name="new_password_2" >
        <br>
        <?php echo $password_message; ?>
        <input type="submit" name="update_password" id="save_details" value="Update Password">
        <br>
    </form>

    <!--Close Account-->
    <h4>Close Account</h4>
    <form action="settings.php">
        <input type="submit" name="close_account" id="close_account" value="Close Account">
    </form>


</div>
