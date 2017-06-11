<?php
        $first_name="default";

        $get_first_name_query = "SELECT first_name FROM users WHERE username = ?";
        /*Create prepared statement*/
        if($stmt = mysqli_prepare($con,$get_first_name_query)){
            
            /*Bind parameters for markers, type 's'/string */
            mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
            /*Execute query*/
            mysqli_stmt_execute($stmt);
            /*Bind Result variables*/
            mysqli_stmt_bind_result($stmt, $first_name);
            /*Fetch values i.e. to bound result variable*/
            mysqli_stmt_fetch($stmt);
            /* Close stmt*/
            mysqli_stmt_close($stmt);
        }


?>