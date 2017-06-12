<?php 

require './includes/header.php';
//session_destroy();
?>



    <div class ="user_details column">
        <a href="#"><img src="
                                <?php 
                                    if(isset($meta_person["profile_pic"])){
                                        echo $meta_person["profile_pic"];
                                    }?>
                             " 
            alt=""></a>
        
    </div>


</body>
</html>
