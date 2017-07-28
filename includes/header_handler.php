<?php
        $meta_person = array();

        $get_first_name_query = "SELECT first_name,last_name,profile_pic,num_posts,num_likes FROM users WHERE username = ?";
        /*Create prepared statement*/
        if($stmt = mysqli_prepare($con,$get_first_name_query)){       
            /*Bind parameters for markers, type 's'/string */
            mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
            /*Execute query*/
            mysqli_stmt_execute($stmt);
            /*Bind Result variables*/
            mysqli_stmt_bind_result($stmt,$meta_person["first_name"],$meta_person["last_name"],$meta_person["profile_pic"],$meta_person["num_posts"],$meta_person["num_likes"]);
            /*Fetch values i.e. to bound result variable*/
            mysqli_stmt_fetch($stmt);
            /* Close stmt*/
            mysqli_stmt_close($stmt);
        }


?>