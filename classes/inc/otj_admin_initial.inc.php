<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib();

if(isset($_POST['submit'])){
    $userid = $_POST['userid'];
    $courseid = $_POST['courseid'];
    if(!preg_match("/^[0-9]*$/", $userid)){
        header("Location: ./../../admin.php");
    } elseif(!preg_match("/^[0-9]*$/", $courseid)){
        header("Location: ./../../admin.php");
    }

    $totalmonths = $_POST['totalmonths'];
    $otjhours = $_POST['otjhours'];
    $eors = $_POST['eors'];
    $coach = $_POST['coach'];
    $mom = $_POST['mom'];
    $errors = [];

    if(!preg_match("/^[0-9]*$/", $totalmonths) || empty($totalmonths)){
        $errors[0][1] = 'error';
    }
    $errors[0][0] = $totalmonths;

    if(!preg_match("/^[0-9]*$/", $otjhours) || empty($otjhours)){
        $errors[1][1] = 'error';
    }
    $errors[1][0] = $otjhours;

    if(!preg_match("/^[a-z A-Z]*$/", $eors) || empty($eors)){
        $errors[2][1] = 'error';
    }
    $errors[2][0] = $eors;

    if(!preg_match("/^[a-z A-Z]*$/", $coach) || empty($coach)){
        $errors[3][1] = 'error';
    }
    $errors[3][0] = $coach;

    if(!preg_match("/^[a-z A-Z]*$/", $mom) || empty($mom)){
        $errors[4][1] = 'error';
    }
    $errors[4][0] = $mom;

    $startdate = $_POST['startdate'];
    $startdate = new DateTime($startdate);
    $startdate = $startdate->format('U');
    $errors[5][0] = $startdate;

    $hpw = $_POST['hpw'];
    $alw = $_POST['alw'];
    $glh = $_POST['glh'];

    if(!preg_match("/^[0-9]*$/", $hpw) || empty($hpw)){
        $errors[6][1] = 'error';
    }
    $errors[6][0] = $hpw;

    if(!preg_match("/^[0-9.]*$/", $alw) || empty($alw)){
        $errors[7][1] = 'error';
    }
    $errors[7][0] = $alw;

    $planfilename = $_POST['planfilename'];
    $filenames = $lib->training_plans();
    $errors[8][1] = 'error';
    foreach($filenames as $filename){
        if($filename[1] == $planfilename){
            $errors[8][1] = '';
        }
    }
    $errors[8][0] = $planfilename;

    $incorrect = false;
    foreach($errors as $err){
        if($err[1] == 'error'){
            $incorrect = true;
        }
    }

    if($incorrect == true){
        $errors[5][0] = date('Y-m-d',$startdate);
        $_SESSION['initialerror'] = $errors;
        header("Location: ./../../otj_setup_admin.php?userid=$userid&courseid=$courseid");
    } elseif($incorrect == false){
        $lib->admin_update_setup($userid, $courseid, $errors);
        $_SESSION['success'] = 'initial-edit';
        header("Location: ./../../admin.php");
    }
    print_r($errors);
}