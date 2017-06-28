<?php
    require '../../config/config.php';

    if(isset($_GET['post_id'])) 
        $post_id = $_GET['post_id'];
    if(isset($_POST['result'])){
         $delete_post_query = "UPDATE posts SET deleted = 'yes' WHERE id = ?";
        if($stmt = mysqli_prepare($con,$delete_post_query)){
            mysqli_stmt_bind_param($stmt, "s",$post_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        
    }

?>