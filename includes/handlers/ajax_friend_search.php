<?php  
    include("../../config/config.php");
    include("../classes/User.php");

    $query = $_POST['query'];
    $userLoggedIn = $_POST['userLoggedIn'];


    /*To split spaces in an array*/ 
    $names = explode(" ", $query);
    $queryCount=0;

    if(strpos($query,"_")!== false){
        $usersReturned_query = "SELECT username,first_name,last_name,profile_pic FROM users WHERE username LIKE ? AND user_closed='no' LIMIT 8";
        if($stmt = mysqli_prepare($con,$usersReturned_query)){
            $pattern = $query."%";
            mysqli_stmt_bind_param($stmt, "s",$pattern);/*SQL pattern*/
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$username,$firstName,$lastName,$profilePic);
            while(mysqli_stmt_fetch($stmt)){
                $queryCount++;
            }
            mysqli_stmt_close($stmt);
        }
    }else{
        if(count($names)==2){
            $usersReturned_query = "SELECT username,first_name,last_name,profile_pic FROM users WHERE (first_name LIKE ? AND last_name LIKE ?) AND user_closed='no' LIMIT 8";
            if($stmt = mysqli_prepare($con,$usersReturned_query)){
                $pattern1 = "%".$names[0]."%";
                $pattern2 = "%".$names[1]."%";
                mysqli_stmt_bind_param($stmt, "ss",$pattern1,$pattern2);/*SQL pattern*/
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$username,$firstName,$lastName,$profilePic);
                while(mysqli_stmt_fetch($stmt)){
                    $queryCount++;
                }

                mysqli_stmt_close($stmt);
            }

    }else{
            /*last resort search*/
            $usersReturned_query = "SELECT username,first_name,last_name,profile_pic FROM users WHERE (first_name LIKE ? OR last_name LIKE ?) AND user_closed='no' LIMIT 8";
            if($stmt = mysqli_prepare($con,$usersReturned_query)){
                $pattern1 = "%".$names[0]."%";
                $pattern2 = "%".$names[0]."%";
                mysqli_stmt_bind_param($stmt, "ss",$pattern1,$pattern2);/*SQL pattern*/
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$username,$firstName,$lastName,$profilePic);
                while(mysqli_stmt_fetch($stmt)){
                    $queryCount++;
                }   
                mysqli_stmt_close($stmt);
            }
        }
    if($query!= ""){
        for($queryCount;$queryCount>0;$queryCount--){
            $user = new User($con, $userLoggedIn);
            
            if($username != $userLoggedIn) {
                $mutual_friends = $user->getMutualFriends($username) . " friends in common";
            }
            else {
                $mutual_friends = "";
            }

            if($user->isFriend($username)) {
                echo "<div class='resultDisplay'>
                        <a href='messages.php?u=" . $username . "' style='color: #000'>
                            <div class='liveSearchProfilePic'>
                                <img src='". $profilePic . "'>
                            </div>

                            <div class='liveSearchText'>
                                ".$firstName . " " . $lastName. "
                                <p style='margin: 0;'>". $username . "</p>
                                <p id='grey'>".$mutual_friends . "</p>
                            </div>
                        </a>
                    </div>";


            }
        }
    }

    }




?>