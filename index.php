<?php 

require './includes/header.php';
//session_destroy();
?>



    <div class ="user_details column">
        <a href="#"><img src="
            <?php if(isset($meta_person["profile_pic"])){echo $meta_person["profile_pic"];}?>" 
           alt="">
        </a>
        <?php if(isset($meta_person["num_posts"])){echo "Posts:". $meta_person["num_posts"]."<br>";}?>
        <?php if(isset($meta_person["num_likes"])){echo "Likes:". $meta_person["num_likes"];}?>  
    </div>

    </div>
</body>
</html>
