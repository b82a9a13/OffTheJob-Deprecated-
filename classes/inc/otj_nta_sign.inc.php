<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib();

if(isset($_POST['submit'])){
    $userid = $_POST['userid'];
    $courseid = $_POST['courseid'];
    $url = $_POST['signature'];
    $error = false;

    if(!preg_match('/^[0-9]*$/', $userid) || empty($userid)){
        header("Location: ../../teacher.php?sign=false");
        $error = true;
    }

    if(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
        header("Location: ../../teacher.php?sign=false");
        $error = true;
    }

    if(!preg_match("/^[a-zA-Z0-9+:;\/,=]*$/", $url) || empty($url)){
        header("Location: ../../teacher.php?sign=false");
        $error = true;
    }

    if($error == false){
        $lib->nta_sign($userid, $courseid, $url);
        header("Location: ../../teacher.php?sign=true");
    }
}