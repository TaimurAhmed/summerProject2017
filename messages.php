<?php
include("./includes/header.php");

$message_obj = new Message($con,$userLoggedIn);

//Username is already taken as value in the assoc array
if(isset($_GET['u'])){
    /*To prevent mismatch in DB e.g. mickey != Mickey but will be stored in DB*/
    /*Can do with a helper functions later to harden DB*/
    $user_to = strtolower($_GET['u']);
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

if(isset($_POST['post_message'])){
    if(isset($_POST['message_body'])){
        $body = mysqli_real_escape_string($con,$_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_to,$body,$date);


    }
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
            echo "<div class='loaded_messages' id='scroll_messages'>";
            echo $message_obj->getMessages($user_to);/*Loaded requested messages*/
            echo "</div>";
        }else{
            echo "<h4>New Message</h4>";
        }
        ?>




        <div class='message_post'>
            <form action="" method="POST">
                <?php
                    if($user_to === "new"){
                        echo "Select the friend you would like to message? <br>";
                        echo "To: <input type='text'>";
                        echo "<div class='results'></div>";
                    }else{
                        echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message... '></textarea>";
                        echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
                    }

                ?>
                
            </form>
            
        </div>

        <!--To prevent message form from scrolling to top when new message is sent--> 
        <script>
            var div = document.getElementById("scroll_messages");
            div.scrollTop = div.scrollHeight;
        </script>
    </div>

    <div class="user_details_column" id="conversations">
        <h4>Conversation</h4>

        <div class="loaded_conversations">
            <?php echo $message_obj->getConvos(); ?>
        </div>
        <br>
        <a href="messages.php?u=new">New Message</a>

    </div>






    