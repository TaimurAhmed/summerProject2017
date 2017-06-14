<?php 
require './includes/header.php';
include("./includes/classes/User.php");
include("./includes/classes/Post.php");


if(isset($_POST['post'])){
     $post = new Post($con,$userLoggedIn);
     $post->submitPost($_POST['post_text'],'none');
}


?>
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

        <div class="main_column column">
            <form class="post_form" action="index.php" method="POST">
                <textarea name="post_text" id="post_text" placeholder="Got something to say ?"></textarea>
                <input type="submit" name = "post" id ="post_button" value = "Post ">
                <hr> 
            </form>
        <?php
        /*
            $user_obj = new User($con,$userLoggedIn) ;
            echo $user_obj->getFirstandLastName();
            echo "<br>";
            echo $user_obj->getUsername();
            echo "<br>";
            echo $user_obj->getNumPosts();
        */
           $post = new Post($con,$userLoggedIn);
           $post->loadPostFriends();

        ?>
        
        <div class="posts_area"></div>
        <div id='#loading'> 
            <i class="fa fa-refresh fa-spin fa-3x fa-fw" ></i>
            <span class="sr-only">Loading...</span>
        </div>

        </div>
        <script>
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';

            $(document).ready(function() {

                $ ('#loading').show();

                /*Original ajax request for loading first posts*/
                $.ajax({
                    url: "includes/handlers/ajax_load_posts.php",
                    type: "POST",
                    data: "page=1&userLoggedIn=" + userLoggedIn,
                    cache:false,

                    success: function(data) {
                        $('#loading').hide();
                        $('.posts_area').html(data);
                    }
                });

                $(window).scroll(function() {
                    var height = $('.posts_area').height; //Height is equal to height of div containing posts
                    var scroll_top = $(this).scrollTop(); //The top bit of wherever we are scrolling
                    var page = $('.post_area').find('.nextPage').val();
                    var noMorePosts = $('posts_area').find('.noMorePosts').val(); 

                    if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                        
                        $('#loading').show();
                    
                        /*Original ajax request for loading first posts*/
                        var ajaxReq = $.ajax ({
                            url:" includes/handlers/ajax_load_posts.php",
                            type: "POST",
                            data: "page="+ page + "&userLoggedIn=" + userLoggedIn,
                            cache:false,

                            success:function(response){
                                $('.posts_area').find('.nextPage').remove();/*Removes current.nextpage*/
                                $('.posts_area').find('.noMorePosts').remove();/*Removes current.nextpage*/
                                $('#loading').hide();
                                $('#post_area').append(response);
                            }
                        });
                    } //End if
                    return false;

                });//End ((window).scroll(function())
            });



        </script>
</div>
</body>
</html>
