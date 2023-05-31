<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */
// Used for the teacher to manage their learners off the job

require_once(__DIR__.'/../../../../../config.php');
use local_offthejob\lib;
require_login();
$lib = new lib();

$file = $_GET['file'];
$userid = $lib->get_current_user()[0];
$courseid = $_GET['courseid'];
if(!preg_match("/^[0-9a-zA-Z.-]*$/", $file) || empty($file)){
    echo("<p>Error Loading File</p>");
    exit();
} elseif(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
    echo("<p>Invalid Courseid</p>");
    exit();
} else {
    $context = context_course::instance($courseid);
    require_capability('local/offthejob:student', $context);
    $filename = explode('-',$file);
    if($filename[0] === $userid && $filename[1] === $courseid){
        header("Content-type: application/pdf");
        include("./../employercomment/$file"); 
    } else {
        echo("<p>Invalid File!</p>");
    }
}
?>