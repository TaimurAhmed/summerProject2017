<?php
class User{
    private $user;
    private $con;
    private $test;

    public function __construct($con,$user){
        $this->con = $con;
        $user_details_query = "SELECT id,first_name,last_name,username FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($con,$user_details_query)){
            $this->user = array();
            /*Bind parameters for markers, type 's'/string */
            mysqli_stmt_bind_param($stmt, "s",$user);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$this->user["id"],$this->user["first_name"],$this->user["last_name"],$this->user["username"]);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    public function getUsername(){
        return $this->user['username']; 
    }

    public function  getNumPosts(){
        $username = $this->user['username'];
        $getNumPosts_query = "SELECT num_posts FROM users WHERE id = ?";
        $row="";
        if($stmt = mysqli_prepare($this->con,$getNumPosts_query)){
            mysqli_stmt_bind_param($stmt, "s",$this->user["id"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$row);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
        return $row;
    }

    public function getFirstandLastName() {
        $username = $this->user['username'];
        $getFirstandLastName_query = "SELECT first_name,last_name FROM users WHERE id = ?";
        $row = array();
        if($stmt = mysqli_prepare($this->con,$getFirstandLastName_query)){
            mysqli_stmt_bind_param($stmt, "s",$this->user["id"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$row["first_name"],$row["last_name"]);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
        return $row["first_name"] . " " . $row["last_name"];
    }
}


?>