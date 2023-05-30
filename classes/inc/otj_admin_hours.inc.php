<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib;
if(isset($_GET['id']) || isset($_POST['id'])){
    $userid = $_GET['userid'];
    $courseid = $_GET['courseid'];
    $id = $_GET['id'];
    if(isset($_POST['userid'])){
        $userid = $_POST['userid'];
    }
    if(isset($_POST['courseid'])){
        $courseid = $_POST['courseid'];
    }
    if(isset($_POST['id'])){
        $id = $_POST['id'];
    }
    if(!preg_match("/^[0-9]*$/", $userid) || empty($userid)){
        header('Location: ./../../admin.php');
    } elseif(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
        header('Location: ./../../admin.php');
    } elseif(!preg_match("/^[0-9]*$/", $id) || empty($id)){
        header('Location: ./../../admin.php');
    }
    $type = $_GET['type'];
    if(!empty($type) || $type !== null){
        if(!preg_match("delete", $type) && !preg_match("update", $type)){
            header('Location: ./../../admin.php');
        }
        if($type == 'update'){
            $array = $lib->get_hour($id);
            $_SESSION['array'] = $array;
            $_SESSION['info'] = [$userid, $courseid, $id];
            header('Location: ./../../otj_hours_admin.php');
        } elseif($type == 'delete'){
            $lib->del_hour($id);
            $_SESSION['success'] = 'delete';
            header("Location: ./../../otj_hours_admin.php?userid=$userid&courseid=$courseid");
        }
    } else {
        $date = $_POST['date'];
        $activity = $_POST['activity'];
        $whatlink = $_POST['whatlink'];
        $impact = $_POST['impact'];
        $duration = $_POST['duration'];
        $initial = $_POST['initial'];
        $checkarray = [];
        $error = false;
        if($date <> null){
            $date = new DateTime($date);
            $date = $date->format('U');
        }else {
            $error = true;
            $checkarray[0] = 'red';
        }
        if(!preg_match("/^[a-zA-Z 0-9.!,\-()\/?#]*$/", $activity) || empty($activity)){
            $error = true;
            $checkarray[1] = 'red';
        }
        if(!preg_match("/^[a-zA-Z .,0-9]*$/", $whatlink) || empty($whatlink)){
            $error = true;
            $checkarray[2] = 'red';
        }
        if(!preg_match("/^[a-zA-Z 0-9.!,\-()\/?#]*$/", $impact) || empty($impact)){
            $error = true;
            $checkarray[3] = 'red';
        }
        if(!preg_match("/^[0-9.]*$/", $duration) || empty($duration)){
            $error = true;
            $checkarray[4] = 'red';
        }
        if(!preg_match("/^[a-zA-Z]*$/", $initial) || empty($initial)){
            $error = true;
            $checkarray[5] = 'red';
        }
        $array = [
            $date,
            $activity,
            $whatlink,
            $impact,
            $duration,
            $initial
        ];
        if($error == false){
            $lib->admin_update_hour($array, $id, $userid, $courseid);
            $_SESSION['success'] = 'update';
            header("Location: ./../../otj_hours_admin.php?userid=$userid&courseid=$courseid");
        } else {
            $_SESSION['info'] = [$userid, $courseid, $id];
            $_SESSION['array'] = $array;
            $_SESSION['checkarray'] = $checkarray;
            header('Location: ./../../otj_hours_admin.php');
        }
    }
}