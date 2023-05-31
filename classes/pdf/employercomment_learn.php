<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
require_login();
$lib = new lib();

$courseid = $_GET['courseid'];
$id = $_GET['id'];
if(!preg_match("/^[0-9]*$/", $courseid) || 
    empty($courseid) || 
    !preg_match("/^[0-9]*$/", $id) || 
    empty($id)) {
    header("Location: ../../learner.php");
    exit();
}
$context = context_course::instance($courseid);
require_capability('local/offthejob:student', $context);

header("Content-type: application/pdf");

$pdf = $lib->get_employer_comment_info_learn($id, $courseid);

$username = $lib->get_current_user()[1];
$coursename = $lib->get_coursename($courseid)->fullname;
header("Content-Disposition:inline;filename=MonthlyActivityRecord-$username-$coursename-EmployerComment.pdf");

include("./employercomment/$pdf");