<?php 
include("./includes/header.php");

if(isset($_GET['id'])){
    $id = $_GET['id'];
}else{
    $id=0; //Perhaps better to redirect !!
}
 
?>

<!--User column-->
<div class ="user_details column">
    <a href="<?php echo $userLoggedIn; ?>"><img src="
        <?php if(isset($meta_person["profile_pic"])){echo $meta_person["profile_pic"];}?>" 
       alt="">
    </a>

    <div class="user_details_left_right">
        <a href="<?php echo $userLoggedIn; ?>">
            <?php if(isset($meta_person["first_name"])){echo $meta_person["first_name"];}            
                  if(isset($meta_person["last_name"])){echo " " . $meta_person["last_name"];}
            ?>
            
        </a>
        <br>
        <?php if(isset($meta_person["num_posts"])){echo "Posts:". $meta_person["num_posts"]."<br>";}?>
        <?php if(isset($meta_person["num_likes"])){echo "Likes:". $meta_person["num_likes"];}?>  
    </div>
</div>


<div class="main_column column" id ="main_column">
    <div class="posts_area">
        <?php
            $post = new Post($con,$userLoggedIn);

        ?>
        
    </div>
</div>