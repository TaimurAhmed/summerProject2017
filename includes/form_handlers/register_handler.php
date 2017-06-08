<?php

//Declaring variables to prevent errors
$fname = ""; //first name
$lname = "";//last name
$em = ""; //email 1
$em2 = ""; //email 2
$password = ""; //password 
$password2 = "";// password 2
$date = "";// Sign up date
$error_array = array();// Holds error messages




if(isset($_POST['register_button'])){

    //Registration form values
    //First name
    $fname= strip_tags($_POST['reg_fname']); //Strip html tags to prevent HTML injections
    $fname = str_replace(' ', '', $fname); // Remove spaces subject variable
    $fname = ucfirst(strtolower($fname)); //Convert string to lower then capitalise first letter
    $_SESSION['reg_fname'] = $fname; //Store reg form values in session variable initialised above

    //Last name
    $lname = strip_tags($_POST['reg_lname']); //Strip html tags to prevent HTML injections
    $lname = str_replace(' ', '', $lname); // Remove spaces subject variable
    $lname = ucfirst(strtolower($lname)); //Convert string to lower then capitalise first letter
    $_SESSION['reg_lname'] = $lname; //Store reg form values in session variable initialised above

    //email
    $em = strip_tags($_POST['reg_email']); //Strip html tags to prevent HTML injections
    $em = str_replace(' ', '', $em); // Remove spaces subject variable
    $em = ucfirst(strtolower($em)); //Convert string to lower then capitalise first letter
    $_SESSION['reg_email'] = $em; //Store reg form values in session variable initialised above

    //email 2
    $em2 = strip_tags($_POST['reg_email2']); //Strip html tags to prevent HTML injections
    $em2 = str_replace(' ', '', $em2); // Remove spaces subject variable
    $em2 = ucfirst(strtolower($em2)); //Convert string to lower then capitalise first letter
    $_SESSION['reg_email2'] = $em2; //Store reg form values in session variable initialised above


    //Passwords, Note: Did not remove white space or case sensitivity (more password options)
    $password = strip_tags($_POST['reg_password']); //Strip html tags to prevent HTML injections
    $password2 = strip_tags($_POST['reg_password2']); //Strip html tags to prevent HTML injections

    //date
    $date = date("Y-m-d");//current date

    if($em == $em2){
        //Check if email is in valid format
        if(filter_var($em,FILTER_VALIDATE_EMAIL)) {
            
            // Is email valid ?
            $em = filter_var($em,FILTER_VALIDATE_EMAIL);
            
            //Check if email already exists
            $e_check = mysqli_query($con, "SELECT email from users WHERE email = '$em'");
            
            //Count the number of rows returned inside e_check
            $num_rows = mysqli_num_rows($e_check);

            if($num_rows > 0){
                array_push($error_array,"Email already being used <br>");
            }
        }
        else{
            array_push($error_array,"Invalid email format <br>");
        }
    }
    else{
        array_push($error_array,"Emails dont match <br>");
    }
    
    if(strlen($fname) >25 || strlen($fname) < 2) {
        array_push($error_array,"Your first name must be between 2 and 25 characters <br>");
    }

    if(strlen($lname) >25 || strlen($lname) < 2) {
        array_push($error_array,"Your last name must be between 2 and 25 characters <br>");
    }

    if($password != $password2) {
        array_push($error_array,"Your passwords do not match <br>");
    }
    else{
        if(preg_match('/[^A-Za-z0-9]/', $password)){
            array_push($error_array,"Your password can only contain standard english characters or numbers <br>");
        }
    }

    if(strlen($password) > 30 || strlen($password) < 5) {
            array_push($error_array,"Your password must be between 5 to 30 characters <br>");
    }


    /**
     * Password Hashing:
     * Please refer to phpinfo.php to set ideal cost for hash as per server specs
     * In this case, we have increased the cost for  BCRYPT from 10 to 12 for demonstration purposes only.
     * Note: BCRYPT algorithim is default, which will always be 60 characters, but DB supports VARCHAR(255)
     * in anticipation of future improvements and changes, as per PHP API.
     * VERY IMPORTANT: Switch to Argon2 hash when it is rolled out with PHP 7.2 late in 2017
     */

    // To set hash cost. Please change as explained above when deployed.
    $options = [
    'cost' => 12,
    ];

    if (empty($error_array)){
        /* Hashing plus salting*/
        $password = password_hash($password,PASSWORD_DEFAULT,$options); 
        /*Create a default username by concatenating first and last name*/
        $username = strtolower($fname . "_" .$lname);
        
        /* Query: Count number of usernames with pattern fname_lastname_% */
        $check_username_query = "SELECT COUNT(username) FROM users WHERE username LIKE ?";
        
        /*Create prepared statement*/
        if($stmt = mysqli_prepare($con,$check_username_query)){
            $pattern = $username . "%";
            
            /*Bind parameters for markers, type 's'/string */
            mysqli_stmt_bind_param($stmt, "s", $pattern);

            /*Execute query*/
            mysqli_stmt_execute($stmt);

            /*Bind Result variables*/
            mysqli_stmt_bind_result($stmt, $result);

            /*Fetch values i.e. to bound result variable*/
            mysqli_stmt_fetch($stmt);

            /* Close stmt*/
            mysqli_stmt_close($stmt);
        }
        /**
         * First user gets fname_lname_1.
         * Next user gets fname_lname_2 (assuming same name)
         */
        $result ++;
        $username = $username . "_" .$result;
        


        /**
         * Default Picture Assignment
         * Write algorithim around final stages to randomly select any using rand
         */
        
        $rand = rand(1,2); //Random number between 1 and 2

        if($rand = 1){
            $profile_pic = '/assets/images/profile_pics/defaults/head_deep_blue.png';
        }else{
            $profile_pic = '/assets/images/profile_pics/defaults/head_carrot.png';

        } 

        /**
         * Insert New User details in DB
         */
        /* Query: Update later to aggregate avoid need for boiler plating !!!!!!!! */
        $insert_new_user_query = "INSERT INTO users VALUES ('', ? , ?, ? , ? , ? , ? , ? , 0, 0 , 'no' , ',')";

        
        /*Create prepared statement*/
        if($stmt = mysqli_prepare($con,$insert_new_user_query)){
            
            /*Bind parameters for markers, type 's'/string */
            mysqli_stmt_bind_param($stmt, "sssssss", $fname,$lname,$username,$em, $password,$date,$profile_pic);
            /*Execute query*/
            mysqli_stmt_execute($stmt);
            /* Close stmt*/
            mysqli_stmt_close($stmt);
        }
        
        /*Sucesfully created user message*/
        array_push($error_array,"<span style='color: #14C800;'> Sucesfully created new user ! </span> <br>");
        
        /* Clear Session Variables*/
        $_SESSION['reg_fname'] = "";
        $_SESSION['reg_lname'] = "";
        $_SESSION['reg_email'] = "";
        $_SESSION['reg_email2'] = "";

    }


}

?>