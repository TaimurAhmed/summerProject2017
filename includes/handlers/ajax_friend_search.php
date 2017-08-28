<?php  
    include("../../config/config.php");
    include("../classes/User.php");

    $query = $_POST['query'];
    $userLoggedIn = $_POST['userLoggedIn'];


    /*To split spaces in an array*/ 
    $names = explode(" ", $query);
    $queryCount=0;
    $username = array();
    $firstName = array();
    $lastName = array();
    $profilePic = array();

    if(strpos($query,"_")!== false){
        $usersReturned_query = "SELECT username,first_name,last_name,profile_pic FROM users WHERE username LIKE ? AND user_closed='no' LIMIT 8";
        if($stmt = mysqli_prepare($con,$usersReturned_query)){
            $pattern = $query."%";
            mysqli_stmt_bind_param($stmt, "s",$pattern);/*SQL pattern*/
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$u,$f,$l,$p);
            while(mysqli_stmt_fetch($stmt)){
                $username[$queryCount] = $u;
                $firstName[$queryCount] = $f;
                $lastName[$queryCount]=$l;
                $profilePic[$queryCount]=$p;
                $queryCount++;
            }
            if($queryCount != 0){
                $queryCount--; 
            }
            mysqli_stmt_close($stmt);
        }
    }else if(count($names)==2){
        $usersReturned_query = "SELECT username,first_name,last_name,profile_pic FROM users WHERE (first_name LIKE ? AND last_name LIKE ?) AND user_closed='no' LIMIT 8";
        if($stmt = mysqli_prepare($con,$usersReturned_query)){
            $pattern1 = "%".$names[0]."%";
            $pattern2 = "%".$names[1]."%";
            mysqli_stmt_bind_param($stmt, "ss",$pattern1,$pattern2);/*SQL pattern*/
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$u,$f,$l,$p);
            while(mysqli_stmt_fetch($stmt)){
                $username[$queryCount] = $u;
                $firstName[$queryCount] = $f;
                $lastName[$queryCount]=$l;
                $profilePic[$queryCount]=$p;
                $queryCount++;
            }
            if($queryCount != 0){
                $queryCount--; 
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
                mysqli_stmt_bind_result($stmt,$u,$f,$l,$p);
                while(mysqli_stmt_fetch($stmt)){
                    $username[$queryCount] = $u;
                    $firstName[$queryCount] = $f;
                    $lastName[$queryCount]=$l;
                    $profilePic[$queryCount]=$p;
                    $queryCount++;
                }
                if($queryCount != 0){
                    $queryCount--; 
                }
                mysqli_stmt_close($stmt);
            }
        }

        $ariaCount=0;//For Aria Tags

        if($query!= "" && $queryCount != 0){
            for($queryCount;$queryCount>=0;$queryCount--){
                $user = new User($con, $userLoggedIn);
                $ariaCount++;
                
                if($username[$queryCount] != $userLoggedIn) {
                    $mutual_friends = $user->getMutualFriends($username[$queryCount]) . " friends in common";
                }
                else {
                    $mutual_friends = "";
                }

                $id = $queryCount;
                $id += 1;
                $id .= 'searchResult';
                
                if($user->isFriend($username[$queryCount])) {
                    echo "<div role='listitem' id='result$ariaCount' aria-labelledby='searchBarResults result$ariaCount' aria-describedby='searchResultItem' class='resultDisplay'>
                            <a href='messages.php?u=" . $username[$queryCount] . "' style='color: #000'>
                                <div class='liveSearchProfilePic'>
                                    <img src='". $profilePic[$queryCount] . "'>
                                </div>

                                <div class='liveSearchText'>
                                    ".$firstName[$queryCount] . " " . $lastName[$queryCount]. "
                                    <p style='margin: 0;'>". $username[$queryCount] . "</p>
                                    <p id='grey'>".$mutual_friends . "</p>
                                </div>
                            </a>
                        </div>";


                }
            }
        }else{
            echo "<div role='listitem' id='result$ariaCount' aria-labelledby='searchBarResults result$ariaCount' aria-describedby='searchResultItem' class='resultDisplay'>
                        <div class='liveSearchProfilePic'>
                            <i class='fa fa-frown-o' aria-hidden='true'>
                                <div class='liveSearchText'>
                                    Meh...couldnt find anything!
                                </div>
                            </i>
                        </div>
                  </div>";
        }
    





?>