<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

//Used for admin initial setup page
require_once(__DIR__.'/../../config.php');

require_login();

$context = context_system::instance();
require_capability('local/offthejob:manager', $context);

use local_offthejob\lib;
$lib = new lib();

$userid = $_GET['userid'];
$courseid = $_GET['courseid'];

if(empty($userid)){
    $userid = $_POST['userid'];
    $courseid = $_POST['courseid'];
    if(!isset($_POST['submit'])){
        header("Location: ./admin.php");
    }
}
if(!preg_match("/^[0-9]*$/", $userid) || 
    empty($userid) || 
    !preg_match("/^[0-9]*$/", $courseid) || 
    empty($courseid)) {
    header("Location: ./admin.php");
}

$username = $lib->get_username($userid);
$coursename = $lib->get_coursename($courseid);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/otj_setup_admin.php'));
$PAGE->set_title('Off The Job - Initial Setup - '.$username->username.' - '.$coursename->fullname);
$PAGE->set_heading('Off The Job - Initial Setup - '.$username->username.' - '.$coursename->fullname);

echo $OUTPUT->header();

$template = (object)[
    'username' => $username->username,
    'coursename' => $coursename->fullname,
    'userid' => $userid,
    'courseid' => $courseid
];
echo $OUTPUT->render_from_template('local_offthejob/admin_setup_title', $template);

$setuparray = $lib->admin_initial($userid, $courseid);
$array = array([
    $setuparray->totalmonths,
    $setuparray->otjhours,
    $setuparray->employerorstore,
    $setuparray->coach,
    $setuparray->managerormentor,
    date('Y-m-d',$setuparray->startdate),
    $setuparray->hoursperweek,
    $setuparray->annuallw,
]);
$filesarray = $lib->training_plans();
$planarray = [];
foreach($filesarray as $filearr){
    if($filearr[1] == $setuparray->planfilename){
        array_push($planarray, [$filearr[0], $filearr[1], 'selected']);
    } else {
        array_push($planarray, [$filearr[0], $filearr[1]]);
    }
}

$template = (object)[
    'otjs' => get_string('otjs', 'local_offthejob'),
    'total_m' => get_string('total_m', 'local_offthejob'),
    'otjh' => get_string('otjh', 'local_offthejob'),
    'eands' => get_string('eands', 'local_offthejob'),
    'coach' => get_string('coach', 'local_offthejob'),
    'morm' => get_string('morm', 'local_offthejob'),
    'submit' => get_string('submit', 'local_offthejob'),
    'clear' => get_string('clear', 'local_offthejob'),
    'nta_sign' => get_string('nta_sign', 'local_offthejob'),
    'btm' => get_string('btm', 'local_offthejob'),
    'startdate' => get_string('startdate', 'local_offthejob'),
    'hpw' => get_string('hpw', 'local_offthejob'),
    'userid' => $userid,
    'courseid' => $courseid,
    'array' => $array,
    'filesarray' => array_values($planarray)
];

//For when an error has occured on inputting values
if(!empty($_SESSION['initialerror'])){
    $initarray = $_SESSION['initialerror'];
    $planfilename = $initarray[9][0];
    $planarray = [];
    foreach($filesarray as $filearr){
        if($filearr[1] == $planfilename){
            array_push($planarray, [$filearr[0], $filearr[1], 'selected']);
        } else {
            array_push($planarray, [$filearr[0], $filearr[1]]);
        }
    }
    $template->filesarray = array_values($planarray);
    $num = 0;
    $pos = 9;
    foreach($initarray as $initarr){
        if($initarr[1] == 'error'){
            $array[0][$num] = $initarr[0];
            $array[0][$pos + $num] = 'red';
        } else {
            $array[0][$num] = $initarr[0];
        }
        $num++;
    }
    $template->array = $array;   
    unset($_SESSION['initialerror']);
    echo("<h2 class='bold text-danger'>Invalid Inputs are highlighted in red!</h2>");
}
echo $OUTPUT->render_from_template('local_offthejob/admin_setup', $template);

echo $OUTPUT->footer();