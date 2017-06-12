<?php 
require './includes/header.php';
?>



    <div class ="user_details column">
        <a href="#"><img src="
            <?php if(isset($meta_person["profile_pic"])){echo $meta_person["profile_pic"];}?>" 
           alt="">
        </a>

        <div class="user_details_left_right">
            <a href="#">
                <?php if(isset($meta_person["first_name"])){echo $meta_person["first_name"];}            
                      if(isset($meta_person["last_name"])){echo " " . $meta_person["last_name"];}
                ?>
                
            </a>
            <br>
            <?php if(isset($meta_person["num_posts"])){echo "Posts:". $meta_person["num_posts"]."<br>";}?>
            <?php if(isset($meta_person["num_likes"])){echo "Likes:". $meta_person["num_likes"];}?>  
        </div>
    </div>

        <div class="main_column column">
            <form class="post_form" action="index.php" method="POST">
                <textarea name="post_text" id="post_text" placeholder="Got something to say ?"></textarea>
                <input type="submit" name = "post" id ="post_button" value = "Post ">
                <hr> 
            </form>
        </div>
</div>
</body>
</html>
