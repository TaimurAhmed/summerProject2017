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
            $fetch_hash_query = "SELECT password FROM users WHERE email = ?";
            if($stmt = mysqli_prepare($con,$fetch_hash_query)){
                /*Fetch hash from DB for relevant credentials*/
                /*Bind parameters for markers, type 's'/string */
                mysqli_stmt_bind_param($stmt, "s",$email);
                /*Execute query*/
                mysqli_stmt_execute($stmt);
                /*Bind Result variables*/
                mysqli_stmt_bind_result($stmt, $result2);
                /*Fetch values i.e. to bound result variable*/
                mysqli_stmt_fetch($stmt);
                /* Close stmt*/
                mysqli_stmt_close($stmt);
                echo "exiting final sql, result: ". $result2 . $email;
        }
        }else{
            /* 0 == Doesnt exist. 2 == Something bad has happened. Figure out a way to deal */
            echo "Either user doesnt exist or something bad has happened !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
            exit();
        }

        if(password_verify($_POST['log_password'],$result2)){
            echo "correct credentials, access granted";
        }else{
            echo "incorrect password, access denied";
            echo "you entered" . $_POST['log_password'];
            echo "corresponding hash:" . $result2;
        }

 
    }

?>