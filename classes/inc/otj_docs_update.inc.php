<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib();

if(isset($_POST['submit'])){
    $userid = $_GET['userid'];
    $courseid = $_GET['courseid'];
    $id = $_GET['id'];
    if(!preg_match("/^[0-9]*$/", $userid)){
        $string = 'Location: ../../teacher.php';
    } elseif(!preg_match("/^[0-9]*$/", $courseid)){
        $string = 'Location: ../../teacher.php';
    } elseif(!preg_match("/^[0-9]*$/", $id)){
        $string = 'Location: ../../teacher.php';
    } else {
        $string = "Location: ../../otj_doc.php?userid=$userid&courseid=$courseid&type=update&form=true&id=$id";
    }
    header($string);
}