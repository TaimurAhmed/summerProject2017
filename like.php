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

            $get_likes_query = "SELECT likes, added_by FROM posts WHERE id = '?'";
            if($stmt = mysqli_prepare($con,$get_likes_query)){
                mysqli_stmt_bind_param($stmt, "s",$post_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$total_likes,$user_liked);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }
            
        ?>  
        
    </body>

</html>