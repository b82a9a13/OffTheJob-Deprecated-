<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib;

if(isset($_POST['submit'])){
    $userid = $lib->get_current_user();
    $userid = $userid[0];
    $courseid = $_GET['courseid'];
    $id = $_POST['id'];
    if(!preg_match("/^[0-9]*$/", $courseid)){
        $string = 'Location: ../../learner.php';
    } elseif(!preg_match("/^[0-9]*$/", $id)){
        $string = 'Location: ../../learner.php';
    } else {
        $string = "Location: ../../otj_doc_learn.php?courseid=$courseid&type=update&form=true&id=$id";
    }
    header($string);
}