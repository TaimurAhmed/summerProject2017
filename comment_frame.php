<html>
    <head>
        <title></title>
        <!--CSS-->
        <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    </head>
    <body>
        <?php 
            require './config/config.php';
            include("./includes/classes/User.php");
            include("./includes/classes/Post.php");

            /*Redirect users who are not logged in*/
            if(isset($_SESSION["username"])){
                $userLoggedIn = $_SESSION["username"]; 

            }else{
                header("Location:register.php");
            }

            require './includes/header_handler.php'; 
        ?>

        <!-- JS -->
        <script>
            function toggle(){
                var element = document.getElementById("comment_section");
                if(element.style.display == "block"){
                    element.style.display = "none";
                }else{
                    element.style.display = "block";
                }
            } 
        </script>
        <?php
            /*Get the id of post*/
            if(isset($__GET["post_id"])){
                $post_id = $__GET["post_id"];
            }
            $comment = array();
            $comment_query = "SELECT added_by,user_to FROM posts WHERE id = ?";
            if($stmt = mysqli_prepare($con,$comment_query)){
                mysqli_stmt_bind_param($stmt, "s",$post_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$comment["added_by"],$comment["user_to"]);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }
            $posted_to = $comment["added_by"];

            if(isset($_POST['postComment' . $post_id])){
                $post_body = $_POST['post_body'];
                $post_body = mysqli_escape_string($con,$post_body);
                $date_time_now = date("Y-m-d H:i:s");
                $insert_comment_query = "INSERT INTO comments VALUES ('',?,?,?,?,'no',?)";
                if($stmt = mysqli_prepare($con,$insert_comment_query)){
                    mysqli_stmt_bind_param($stmt, "sssss",$post_body, $userLoggedIn, $posted_to, $date_time_now, $post_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
                echo "<p>Comment Posted ! <p>";
            }
        ?>
        <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
            <textarea name="post_body"></textarea>
            <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
        </form>
            
        
    </body>
</html>