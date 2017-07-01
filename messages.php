<?php
include("./includes/header.php");

$message_obj = new Message($con,$userLoggedIn);

//Username is already taken as value in the assoc array
if(isset($_GET['u'])){
    $user_to = $_GET['u'];
}else{
    //Assuming user has not messaged anyone yet
    $user_to = $message_obj->getMostRecentUser();
    if($user_to === false){
        $user_to = 'new';
    }
}

if($user_to != "new"){
    $user_to_obj = new User($con,$user_to); 
}

?>
    <!-- Same as one from index.php. Consider abstracting?-->
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

    <div class = "main_column column" id="main_column">
        <?php
        /*If user_to is not new and msg is being sent existing user*/
        if($user_to != "new"){
            echo "<h4> You and <a href='$user_to'>". $user_to_obj->getFirstandLastName()."</a> </h4><hr><br>"; 
        }
        ?>
        
    </div>



    