<?php 

include("../../config/config.php");
include("../../includes/classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];
$names = explode(" ", $query);/*Returns array split at target i.e. a " " /space*/

$i = 0;
$username = array();
$first_name = array();
$last_name = array();
$profile_pic = array();
$mutual_friends=""; //For some strange reason needed to be added here to prevent a search error for undefined variable


if(strpos($query, '_') !== false){ 
            /*If query contains underscore, assume searching for uName*/
            $userSearchQuery = "SELECT username,first_name,last_name,profile_pic FROM users WHERE username LIKE ? AND user_closed='no' LIMIT 8";
            $pattern = $query."%";
            if($stmt = mysqli_prepare($con,$userSearchQuery)){
                mysqli_stmt_bind_param($stmt, "s",$pattern);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$username[$i],$first_name[$i],$last_name[$i],$profile_pic[$i]);
                while(mysqli_stmt_fetch($stmt)){
                    $i++;
                    mysqli_stmt_bind_result($stmt,$username[$i],$first_name[$i],$last_name[$i],$profile_pic[$i]);
                }
                mysqli_stmt_close($stmt);
            }
}else if(count($names) == 2){
            /*Two seperate words means that it probably a name*/
            $userSearchQuery = "SELECT username,first_name,last_name,profile_pic FROM users WHERE (first_name LIKE ? AND last_name LIKE ? ) AND user_closed='no' LIMIT 8";
            $pattern = $names[0]."%";
            $patternTwo = $names[1]."%";
            if($stmt = mysqli_prepare($con,$userSearchQuery)){
                mysqli_stmt_bind_param($stmt, "ss",$pattern,$patternTwo);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$username[$i],$first_name[$i],$last_name[$i],$profile_pic[$i]);
                while(mysqli_stmt_fetch($stmt)){
                    $i++;
                    mysqli_stmt_bind_result($stmt,$username[$i],$first_name[$i],$last_name[$i],$profile_pic[$i]);

                }
                mysqli_stmt_close($stmt);
            }
 
}else{
            /*Last resort: one word only search name*/
            $userSearchQuery = "SELECT username,first_name,last_name,profile_pic FROM users WHERE (first_name LIKE ? OR last_name LIKE ?) AND user_closed='no' LIMIT 8";
            $pattern = $names[0]."%";
            if($stmt = mysqli_prepare($con,$userSearchQuery)){
                mysqli_stmt_bind_param($stmt, "ss",$pattern,$pattern);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$username[$i],$first_name[$i],$last_name[$i],$profile_pic[$i]);
                while(mysqli_stmt_fetch($stmt)){
                    $i++;
                    mysqli_stmt_bind_result($stmt,$username[$i],$first_name[$i],$last_name[$i],$profile_pic[$i]);
                }
                mysqli_stmt_close($stmt);
            }
}

/*i.e. if they are searching for something and NOT nothing*/
if($query != ""){


    for($n=0; $n < $i; $n++) {
        $user = new User($con, $userLoggedIn);

        if($username[$n] != $userLoggedIn)
            $mutual_friends = $user->getMutualFriends($username[$n]) . " friends in common";
        else 
            $mutual_friends == "";

        echo "<div class='resultDisplay'>
                    <a href='" . $username[$n] . "' style='color: green'>
                        <div class='liveSearchProfilePic'>
                            <img src='" . $profile_pic[$n]."'>
                        </div>

                        <div class='liveSearchText'>
                            " . $first_name[$n] . " " . $last_name[$n] . "
                            <p>" . $username[$n] ."</p>
                            <p id='grey'>" . $mutual_friends ."</p>
                        </div>
                    </a>
                </div>";

    }


}







?>