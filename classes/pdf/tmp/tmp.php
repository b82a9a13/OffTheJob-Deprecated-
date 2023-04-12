<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */
// Used for the teacher to view employer comment on MAR

require_once(__DIR__.'/../../../../../config.php');

use local_offthejob\lib;

require_login();

$lib = new lib();

$enrolss = $lib->get_enrolments();
$context = context_course::instance($enrolss[0][2]);
require_capability('local/offthejob:teacher', $context);

$file = $_GET['file'];
$userid = $_GET['userid'];
$courseid = $_GET['courseid'];
if(!preg_match("/^[0-9a-zA-Z.-]*$/", $file) || empty($file)){
    echo("<p>Error Loading File</p>");
    exit();
} elseif(!preg_match("/^[0-9]*$/", $userid) || empty($userid)){
    echo("<p>Invalid Userid</p>");
    exit();
} elseif(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
    echo("<p>Invalid Courseid</p>");
    exit();
} else {
    $filename = explode('-',$file);
    if($filename[0] === $userid && $filename[1] === $courseid){
        header("Content-type: application/pdf");
        include("./../employercomment/$file"); 
    } else {
        echo("<p>Invalid File!</p>");
    }

}
?>