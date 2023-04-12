<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib;

if(isset($_POST['submit'])){
    $userid = $_GET['userid'];
    $courseid = $_GET['courseid'];
    $id = $_GET['id'];
    if(!preg_match("/^[0-9]*$/", $userid)){
        $string = 'Location: ../../teacher.php';
    } elseif(!preg_match("/^[0-9]*$/", $courseid)){
        $string = "Location: ../../teacher.php";
    } elseif(!preg_match("/^[0-9]*$/", $id)){
        $string = "Location: ../../teacher.php";
    } else {
        if($lib->check_employer_comment_info($id) === false){
            $text = $lib->get_doc_id_ecom($id);
            unlink("./../pdf/employercomment/$text");        
        }
        $lib->del_doc_id($id);
        $string = "Location: ../../otj_doc.php?userid=$userid&courseid=$courseid&type=delete&form=false&id=$id";
    }
    header($string);
}