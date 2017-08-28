<?php

include("./includes/header.php");

if(isset($_GET['q'])){
    $query = $_GET['q'];
}else{
    $query = "";
}

/*Default to 'name' if type not specified  */
if(isset($_GET['type'])){
    $type = $_GET['type'];
}else{
    $type = "name";
}


?>

<div class="main_column column" id="main_column">
    <?php
        if($query === "" || is_null($query)){
            echo "You searched for nothing so you got nothing. Try actually searching for something";
        }else{


            /*To split spaces in an array*/ 
            $names = explode(" ", $query);
            $queryCount=0;
            $searchHits = false;
            $n;
            $username = array();
            $firstName = array();
            $lastName = array();
            $profilePic = array();

            if($type == "username"){
                $usersReturned_query = "SELECT username,first_name,last_name,profile_pic FROM users WHERE username LIKE ? AND user_closed='no'";
                if($stmt = mysqli_prepare($con,$usersReturned_query)){
                    //echo "hello1";
                    $pattern = $query."%";
                    mysqli_stmt_bind_param($stmt, "s",$pattern);/*SQL pattern*/
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt,$u,$f,$l,$p);
                    $n=mysqli_stmt_num_rows($stmt);
                    while(mysqli_stmt_fetch($stmt)){
                        $username[$queryCount] = $u;
                        $firstName[$queryCount] = $f;
                        $lastName[$queryCount]=$l;
                        $profilePic[$queryCount]=$p;
                        $queryCount++;
                    }
                    $n=mysqli_stmt_num_rows($stmt);
                    //If search actually found anything
                    if($queryCount > 0){ $searchHits = true; }
                    //For iterating through results  later
                    if($queryCount != 0){$queryCount--;}
                    mysqli_stmt_close($stmt);
                }
            }else if(count($names)==3){
                $usersReturned_query = "SELECT username,first_name,last_name,profile_pic FROM users WHERE (first_name LIKE ? AND last_name LIKE ?) AND user_closed='no'";
                if($stmt = mysqli_prepare($con,$usersReturned_query)){
                    //echo "hello2";
                    $pattern1 = "%".$names[0]."%";
                    $pattern2 = "%".$names[2]."%"; // ! because there are three strings and we want the last one
                    mysqli_stmt_bind_param($stmt, "ss",$pattern1,$pattern2);/*SQL pattern*/
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt,$u,$f,$l,$p);
                    $n=mysqli_stmt_num_rows($stmt);
                    while(mysqli_stmt_fetch($stmt)){
                        $username[$queryCount] = $u;
                        $firstName[$queryCount] = $f;
                        $lastName[$queryCount]=$l;
                        $profilePic[$queryCount]=$p;
                        $queryCount++;
                    }
                    $n=mysqli_stmt_num_rows($stmt);
                    //If search actually found anything
                    if($queryCount > 0){ $searchHits = true; }
                    //For iterating through results  later
                    if($queryCount != 0){$queryCount--;}
                    mysqli_stmt_close($stmt);
                }
            }else if(count($names)==2){
                $usersReturned_query = "SELECT username,first_name,last_name,profile_pic FROM users WHERE (first_name LIKE ? AND last_name LIKE ?) AND user_closed='no'";
                if($stmt = mysqli_prepare($con,$usersReturned_query)){
                    //echo "hello3";
                    $pattern1 = "%".$names[0]."%";
                    $pattern2 = "%".$names[1]."%"; // ! because there are two strings and we want the last one
                    mysqli_stmt_bind_param($stmt, "ss",$pattern1,$pattern2);/*SQL pattern*/
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt,$u,$f,$l,$p);
                    $n=mysqli_stmt_num_rows($stmt);
                    while(mysqli_stmt_fetch($stmt)){
                        $username[$queryCount] = $u;
                        $firstName[$queryCount] = $f;
                        $lastName[$queryCount]=$l;
                        $profilePic[$queryCount]=$p;
                        $queryCount++;
                    }
                    $n=mysqli_stmt_num_rows($stmt);
                    //If search actually found anything
                    if($queryCount > 0){ $searchHits = true; }
                    //For iterating through results  later
                    if($queryCount != 0){$queryCount--;}
                    mysqli_stmt_close($stmt);
                }
            }else if(count($names)){
                    /*last resort search*/
                    $usersReturned_query = "SELECT username,first_name,last_name,profile_pic FROM users WHERE (first_name LIKE ? OR last_name LIKE ?) AND user_closed='no'";
                    if($stmt = mysqli_prepare($con,$usersReturned_query)){
                        //echo "hello4";
                        $pattern1 = "%".$names[0]."%";
                        $pattern2 = "%".$names[0]."%";//Assuming a one word search  
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
                        $n=mysqli_stmt_num_rows($stmt);
                        //If search actually found anything
                        if($queryCount > 0){ $searchHits = true; }
                        //For iterating through results  later
                        if($queryCount != 0){$queryCount--;}
                        mysqli_stmt_close($stmt);
                    }
                }



                echo "<p role='alert' aria-relevant='polite' title='Links to use alternative search criteria are provided below' id='grey'>Try searching for:</p>";
                        echo "<a aria-label='searchByName' role='link' title='Search using a name' href='search.php?q=" . $query ."&type=name'>Names</a>, <a aria-label='searchByUserName' role='link' title='Search using a username' href='search.php?q=" . $query ."&type=username'>Usernames</a><br><br><hr id='search_hr'>";

                if(!$n){
                    echo "Couldnt find anyone with ". $type. " like: ". $query;
                }else{
                        echo $n . " results found:".$queryCount." <br> <br>";
                        


                        $ariaCount=0;//Aria Search Result id's   
                        for($i=0;$i<=$queryCount;$i++){
                            $ariaCount++;
                            $user_obj = new User($con, $userMeta['username']);//From header file
                            $button = "";
                            $mutual_friends = "";
                            //echo "HERE" . $userMeta['username'] . $username[$i];
                            if($userMeta['username'] != $username[$i]) {//If not the user themselves
                                //Generate button depending on friendship status and with individual names...(class from bootstrap)
                                if($user_obj->isFriend($username[$i])){
                                    $button = "<input aria-label='remove ".$firstName[$i]."AsFriend' role='button' title='Click to remove ".$firstName[$i]." as friend' type='submit' name='" . $username[$i] . "' class='danger' value='Remove Friend'>";
                                }else if($user_obj->didRecieveRequest($username[$i])){
                                    $button = "<input aria-label='respondTo".$firstName[$i]."FriendRequest' role='button' title='Click to respond to ".$firstName[$i]."&#039s friend request' type='submit' name='" . $username[$i] . "' class='warning' value='Respond to request'>";
                                }else if($user_obj->didSendRequest($username[$i])){
                                    $button = "<input aria-label='friendRequestSentTo".$firstName[$i]."' title='Friend Request sent to ".$firstName[$i]."' type='submit' class='default' value='Request Sent'>";
                                }else{ 
                                    $button = "<input aria-label='add".$firstName[$i]."AsFriend' role='button' title='Click to add  ".$firstName[$i]."' type='submit' name='" . $username[$i] . "' class='success' value='Add Friend'>";
                                }
                            }
                            $mutual_friends = $user_obj->getMutualFriends($username[$i]) . " friends in common";


                            //Button forms
                            if(isset($_POST[$username[$i]])) {

                                if($user_obj->isFriend($username[$i])) {
                                    $user_obj->removeFriend($username[$i]);
                                    header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

                                }
                                else if($user_obj->didRecieveRequest($username[$i])) {
                                    header("Location: requests.php");
                                }
                                else if($user_obj->didSendRequest($username[$i])) {

                                }
                                else {
                                    $user_obj->sendRequest($username[$i]);
                                    header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                                }

                            }



                        echo "<div id='searchResult$ariaCount' aria-lablledby='searchResult$ariaCount' aria-describedby='singeResult' class='search_result_indiv'>
                                    
                                    <div id='searchResultControls$ariaCount' aria-lablledby='searchResult$ariaCount searchResultControls$ariaCount' aria-describedby='controlFriendShip' class='searchPageFriendButtons'>
                                        <form action='' method='POST'>
                                            " . $button . "
                                            <br>
                                        </form>
                                    </div>


                                    <div id='searchResultProfilePicture$ariaCount' aria-lablledby='searchResult$ariaCount searchResultProfilePicture$ariaCount' aria-describedby='userProfilePicture' class='result_profile_pic'>
                                        <a aria-label='linkToPersonalWall' role='link' title='Click to go to ".$firstName[$i]."&#039s profile'  href='" . $username[$i] ."'><img src='". $profilePic[$i] ."' style='height: 100px;'></a>
                                    </div>

                                    <a aria-label='linkToProfile' role='link' title='Click to go to ".$firstName[$i]."&#039s profile' href='" . $username[$i] ."'> " . $firstName[$i] . " " . $lastName[$i] . "
                                    <p id='grey'> " . $username[$i] ."</p>
                                    </a>
                                    <br>
                                    " . $mutual_friends ."
                                    <br>

                                </div>
                                <hr id='search_hr'>";

                        }
                }




        }
    ?>
</div>