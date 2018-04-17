<?php
session_start();
//require_once "DbConnection.php";
$obj = new stdClass();
$obj->user = false;
$obj->failmess = false;
$obj->message = '';
if (isset($_SESSION['user'])){
    if (isset($_GET['logout'])){
        unset($_SESSION['user']);
        session_destroy();
    } else {
        $obj->user = true;
        echo json_encode($obj);
    }
} else {
    // handle login
    if (isset($_POST['username']) && isset($_POST['password'])){
        $username = $_POST['username'];
        $password = $_POST['password'];


        $username = mysql_sanitize_string($con, $username);
        $password = mysql_sanitize_string($con, $password);

        $Hashed = hash('tiger192,3',$password);

        $sql = "SELECT * FROM `Users` WHERE `Username`='$username'";
        $result = $con->query($sql);

        if (!$result){
            // error message
            $failmess = "Whole query ".$sql."<br>";
            echo $failmess;
            die('Invalid query: '.mysqli_error($con));
        } else {
            $count = $result->num_rows;
            if ($count == 1) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['Password'] == $Hashed) {
                        $_SESSION["user"] = $username;
                        $obj->failmess = false;
                        $obj->message = 'Invalid Username/Password';
                        echo json_encode($obj);
                    } else {
                        $obj->failmess = true;
                        echo json_encode($obj);
                    }
                }
            }
        }
    } else {
        echo json_encode($obj);
    }
}