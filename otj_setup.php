<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */
// Used for the teacher to manage their learners off the job

require_once(__DIR__.'/../../config.php');

use local_offthejob\lib;

require_login();

$lib = new lib;

$enrolss = $lib->get_enrolments();
$context = context_course::instance($enrolss[0][2]);
require_capability('local/offthejob:teacher', $context);

$userid = $_GET['userid'];
$courseid = $_GET['courseid'];

$username = $lib->get_username($userid)->username;
$coursename = $lib->get_coursename($courseid)->fullname;

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/otj_setup.php'));
$PAGE->set_title("Off The Job Setup - $username - $coursename");
$PAGE->set_heading("Off The Job Setup - $username - $coursename");

echo $OUTPUT->header();

if(isset($_GET['total'])){
    $total = $_GET['total'];
}
if(!empty($total)){
    $error = 'Invalid Input: ';
    $int = 0;
    while($int < $total){
        $input = $_GET["input$int"];
        if($input == 'totalmonths'){
            $error = "$error Total Months,";
        } elseif ($input == 'otjhours'){
            $error = "$error Off The Job Hours,";
        } elseif ($input == 'eors'){
            $error = "$error Employer Or Store,";
        } elseif ($input == 'coach'){
            $error = "$error Coach,";
        } elseif ($input == 'mom'){
            $error = "$error Manager Or Mentor,";
        } elseif ($input == 'url'){
            $error = "$error Signature,";
        } elseif ($input == 'startdate'){
            $error = "$error Start Date,";
        } elseif ($input == 'hpw'){
            $error = "$error Hours Per Week,";
        } elseif ($input == 'planfilename'){
            $error = "$error Training Plan,";
        }
        $int++;
    }
}

$array = ['','','','',''];
$int2 = 0;
if(isset($_GET["text$int2"])){
    while($int2 < 5){
        $array[$int2] = $_GET["text$int2"];
        $int2++;
    }
}
if(!empty($_GET['date'])){
    $array[5] = date('Y-m-d',$_GET['date']);
}

if(!empty($_GET['hpw'])){
    $array[6] = $_GET['hpw'];
}

if(!empty($_GET['alw'])){
    $array[7] = $_GET['alw'];
}

$filesarray = $lib->training_plans();

$template = (object)[
    'username' => $username,
    'coursename' => $coursename,
    'userid' => $userid,
    'courseid' => $courseid,
    'array' => array_values(array($array)),
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
    'pick_t' => get_string('pick_t', 'local_offthejob'),
    'filesarray' => array_values($filesarray)
];
if(isset($error)){
    $template->error = $error;
}
echo $OUTPUT->render_from_template('local_offthejob/setup', $template);

echo $OUTPUT->footer();
echo("<script src='./classes/js/sign.js'></script>");