<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;

require_login();
$lib = new lib;
$enrolss = $lib->get_enrolments();
$context = context_course::instance($enrolss[0][2]);
require_capability('local/offthejob:teacher', $context);

$courseid = $_GET['courseid'];
$userid = $_GET['userid'];
$id = $_GET['id'];
if(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
    header("Location: ../../teacher.php");
    exit();
} elseif (!preg_match("/^[0-9]*$/", $userid) || empty($userid)){
    header("Location: ../../teacher.php");
    exit();
} elseif(!preg_match("/^[0-9]*$/", $id) || empty($id)){
    header("Location: ../../teacher.php");
    exit();
}

header("Content-type: application/pdf");


$pdf = $lib->get_employer_comment_info($id);

$username = $lib->get_username($userid)->username;
$coursename = $lib->get_coursename($courseid)->fullname;

header("Content-Disposition:inline;filename=MonthlyActivityRecord-$username-$coursename-EmployerComment.pdf");

include("./employercomment/$pdf");