<?php
    if(isset($_POST['login_button'])) {
        $email = filter_var($_POST['log_email'],FILTER_SANITIZE_EMAIL); //Sanitize email in assoc array

        $_SESSION['log_email'] = $email; // Store email in session variable


        /*Abstract hashing to some kind of function? !!!!!!!!!!!!!!!!!!!!!!*/

        $options = [
            'cost' => 12,
        ];
        $password = password_hash($_POST['log_password'],PASSWORD_DEFAULT,$options);


        $match_login_credentials_query = "SELECT COUNT(*) FROM users WHERE email = ? AND password = ?";
        /*Create prepared statement*/
        if($stmt = mysqli_prepare($con,$match_login_credentials_query)){
            
            /*Bind parameters for markers, type 's'/string */
            mysqli_stmt_bind_param($stmt, "ss",$email,$password);
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
            echo "Found it";
        }else{
            echo "Did not find it";
        }

 
    }

?>