<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib;
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $type = $_GET['type'];
    $courseid = $_GET['courseid'];
    $userid = $_GET['userid'];
    if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
        $string = 'Location: ../../otj_hours.php';
    } 
    elseif (!preg_match("/^[a-z]*$/", $type) || empty($type)){
        $string = 'Location: ../../otj_hours.php';
    } 
    elseif (!preg_match("/^[0-9]*$/", $userid) || empty($userid)){
        $string = 'Location: ../../otj_hours.php';
    }
    elseif (!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
        $string = 'Location: ../../otj_hours.php';
    }
    elseif ($type == 'update'){
        $data = $lib->get_hour($id);
        $_SESSION['hoursarray'] = array($data);
        $string = "Location: ../../otj_hours.php?input=update&userid=$userid&courseid=$courseid";
        $lib->del_hour($id);
    }
    elseif ($type == 'delete'){
        $string = "Location: ../../otj_hours.php?input=delete&userid=$userid&courseid=$courseid";
        $lib->del_hour($id);
    }
    header($string);
}