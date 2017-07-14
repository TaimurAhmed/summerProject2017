<?php 
include("includes/header.php");
//include("includes/settings_handler.php"); UNCOMMENT LATER !!!!

?>

<div class="main_column column ">

    <h4> Account Settings</h4>
    
    <?php
        echo "<img src='" .$userMeta['profile_pic']. "'/>";
    ?>
    <br>
    <a href="upload.php">Upload New Profile Picture</a><br><br><br>
</div>
