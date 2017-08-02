<?php 
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");

?>

<div class="main_column column ">

    <h4> Account Settings Page</h4>
    
    <?php
        echo "<img aria-hidden='true' src='" .$userMeta['profile_pic']. "' class='small_profile_pics'/>";
    ?>
    <br>
    <a aria-label='Link for Uploading and Cropping New Photograph' role='Link for Uploading and Cropping New Photograph' title='Link for Uploading and Cropping New Photograph' href="upload.php">Upload New Profile Picture</a><br><br><br>

    <h5> Modify User Details</h5>
    <br> 
    


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
    <form action="settings.php" method="POST" class=settings_form_all>
        First Name: <input aria-label='Type a new First Name Here' role='Type a new First Name Here' title='Type a new First Name Here' type="text" name="first_name" value="<?php echo $first_name; ?>" id="settings_input">
        <br>
        Last Name: <input aria-label='Type a new Last Name Here' role='Type a new Last Name Here' title='Type a new Last Name Here' type="text" name="last_name" value="<?php echo $last_name; ?>" id="settings_input">
        <br>
        Email: <input aria-label='Type new email here' role='Type new email here' title='Type new email here' type="text" name="email" value="<?php echo $email; ?>" id="settings_input">
        <br>
        <input type="submit" name="update_details" id="save_details" value="Update Details" class="warning settings_submit">
        <br>
        <?php echo $message; ?>
    </form>
    
    <br><br><br>
    <!--Encryped user meta data-->
    <h5>Change Password</h5>
    <br>
    <form action="settings.php" method="POST">
        Old Password: <input type="password" name="old_password" id="settings_input">
        <br>
        New Password: <input type="password" name="new_password_1" id="settings_input">
        <br>
        Confirm New Password: <input type="password" name="new_password_2" id="settings_input">
        <br>
        <?php echo $password_message; ?>
        <input type="submit" name="update_password" id="save_details" value="Update Password" class="warning settings_submit">
        <br>
    </form>

    <br><br><br>
    <!--Close Account-->
    <h5>Close Account</h5>
    <br>
    <form action="settings.php" method="POST">
        <input type="submit" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">
    </form>


</div>
