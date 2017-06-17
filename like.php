<html>
    
    <head>
        <title></title>
        <!--CSS-->      
        <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
        <link rel="stylesheet" type="text/css" href="./assets/css/comments.css">
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

            /*Get the id of post*/
            if(isset($_GET["post_id"])){
                $post_id = $_GET["post_id"];
            }
            /*Likes for a particular post*/
            $get_likes_query = "SELECT likes, added_by FROM posts WHERE id = ?";
            if($stmt = mysqli_prepare($con,$get_likes_query)){
                mysqli_stmt_bind_param($stmt, "s",$post_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$total_likes,$user_liked);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }
            /*Corresponding user data to liked data (needs a join so badly)*/
            $user_details_query = "SELECT * FROM users WHERE username = ?";
            /*
            prepared statement
             */
            
            //Like Button 



            //Unlike Button
            
            //Check for previous likes
            $check_for_previous_likes_query = "SELECT * FROM likes WHERE username = ? AND post_id = ?";
            $num_row=0;
            /*Get number of rows of this query!!!*/

            if($num_row>0){
                echo '<form action="like.php?post_id=' .  $post_id . '" method="POST ">
                        <input type="submit" class="comment_like" name="unlike_button" value="Unlike ">
                        <div class="like_value"> 
                            '.$total_likes.' Likes
                        </div> 
                      </form> 
                     ';
            }else{
                echo '<form action="like.php?post_id=' .  $post_id . '" method="POST ">
                        <input type="submit" class="comment_like" name="like_button" value="L ike ">
                        <div class="like_value"> 
                            '.$total_likes.' Likes
                        </div> 
                      </form> 
                     ';
            }


        ?>  
        
    </body>

</html>