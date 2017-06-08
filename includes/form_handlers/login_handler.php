<?php
    if(isset($_POST['login_button'])) {
        $email = filter_var($_POST['log_email'],FILTER_SANITIZE_EMAIL); //Sanitize email in assoc array

        $_SESSION['log_email'] = $email; // Store email in session variable


        /*Abstract hashing to some kind of function? !!!!!!!!!!!!!!!!!!!!!!*/
        
        $options = [
            'cost' => 12,
        ];

        /**
         * Defensive: Check that DB integrity has not been compromised.
         * Consider : Turning account off ???????????????????!!!!!!!!!!
         */
        $count_valid_login_credentials_query = "SELECT COUNT(*) FROM users WHERE email = ?";
        /*Create prepared statement*/
        if($stmt = mysqli_prepare($con,$count_valid_login_credentials_query)){
            
            /*Bind parameters for markers, type 's'/string */
            mysqli_stmt_bind_param($stmt, "s",$email);
            /*Execute query*/
            mysqli_stmt_execute($stmt);
            /*Bind Result variables*/
            mysqli_stmt_bind_result($stmt, $result);
            /*Fetch values i.e. to bound result variable*/
            mysqli_stmt_fetch($stmt);
            /* Close stmt*/
            mysqli_stmt_close($stmt);
        }


        if($result === 1){
            /*Create prepared statement*/
            $fetch_hash_query = "SELECT password,id FROM users WHERE email = ?";
            if($stmt = mysqli_prepare($con,$fetch_hash_query)){
                /*Fetch hash from DB for relevant credentials*/
                /*Bind parameters for markers, type 's'/string */
                mysqli_stmt_bind_param($stmt, "s",$email);
                /*Execute query*/
                mysqli_stmt_execute($stmt);
                /*Bind Result variables*/
                mysqli_stmt_bind_result($stmt, $result2, $id);
                /*Fetch values i.e. to bound result variable*/
                mysqli_stmt_fetch($stmt);
                /* Close stmt*/
                mysqli_stmt_close($stmt);
                
                /*If incorrect password*/
                if(! password_verify($_POST['log_password'],$result2)){
                 array_push($error_array, "Email or password credentials are incorrect <br>");
                }else{
                    /*Update account based on surrogate key*/
                    $reopen_account_query = "UPDATE users SET user_closed = 'no' WHERE id = ? ";
                    if($stmt = mysqli_prepare($con,$reopen_account_query)){
                        /*Bind parameters for markers, type 's'/string */
                        mysqli_stmt_bind_param($stmt, "s",$id);
                        /*Execute query*/
                        mysqli_stmt_execute($stmt);
                        /* Close stmt*/
                        mysqli_stmt_close($stmt);
                    }


                    header("location: index.php");
                }
            }
        }else{
            /*Incorrect email*/
            array_push($error_array, "Email or password credentials are incorrect <br>");//Consider adding one for DB integrity !!!!!!!!!!!!!!!!!!!!!!!
        }



 
    }

?>