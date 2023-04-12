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

$username = $lib->get_username($_GET['userid']);
$coursename = $lib->get_coursename($_GET['courseid']);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/otj_hours.php'));
$PAGE->set_title(get_string('otjh', 'local_offthejob').' - '.$username->username .' - Add new record');
$PAGE->set_heading(get_string('otjh', 'local_offthejob').' - '.$username->username.' - Add new record');

echo $OUTPUT->header();

//Check if input is set and then checks the value of input
if(isset($_GET['input'])){
    $error = true;
    $check = $_SESSION['hourscheck'];
    if(in_array(1, $check)){
        $string = '<u>Invalid:</u>';
    }
    if($check[6] === 1){
        $string .= '<br>Please fill out all the fields';
    } 
    if ($check[0] === 1){
        $string .= '<br> Activity';
    }
    if ($check[1] === 1){
        $string .= '<br> What unit does this link to?';
    }
    if ($check[2] === 1){
        $string .= '<br> What impact will this have in your role?';
    }
    if ($check[3] == 1){
        $string .= '<br> Duration';
    }
    if ($check[4] == 1){
        $string .= '<br> Initial';
    }
    if ($check[5] == 1){
        $string .= '<br> Date';
    }
    $inputtype = $_GET['input'];
    if ($inputtype === 'success'){
        $string .= '<br>Successful Input';
        $error = false;
    }
    if ($inputtype === 'update'){
        $string = 'The record for updating is above. It has been removed from the table and will be added back once submitted';
    }
    if ($inputtype === 'delete'){
        $string = 'The record selected for deletion has been deleted';
    }
    if($error == true){
        $valuesarray = $_SESSION['hoursarray'][0];
        $date = date('Y-m-d',$valuesarray[0]);
        $activity = $valuesarray[1];
        $whatlink = $valuesarray[2];
        $impact = $valuesarray[3];
        $duration = $valuesarray[4];
        $initial = $valuesarray[5];
    } elseif($error == false){
        $date = null;
        $activity = '';
        $whatlink = '';
        $impact = '';
        $duration = '';
        $initial = '';
    }
    unset($_SESSION['hoursarray']);
    unset($_SESSION['hourscheck']);
}

$modulesarray = $lib->plan_json_modules($_GET['userid'], $_GET['courseid']);
$num = 0;
foreach($modulesarray as $modarr){
    if($modarr[0] == $whatlink){
        $modulesarray[$num][1] = 'selected';
    }
    $num++;
}

$template = (object)[
    'title' => get_string('otjh_title', 'local_offthejob'),
    'lname' => get_string('learner_name','local_offthejob'),
    'qual' => get_string('qual', 'local_offthejob'),
    'date' => get_string('date', 'local_offthejob'),
    'activity' => get_string('activity', 'local_offthejob'),
    'what_unit' => get_string('what_unit', 'local_offthejob'),
    'duration' => get_string('duration', 'local_offthejob'),
    'initial' => get_string('initial', 'local_offthejob'),
    'add_newr' => get_string('add_newr', 'local_offthejob'),
    'remove_r' => get_string('remove_r', 'local_offthejob'),
    'btm' => get_string('btm', 'local_offthejob'),
    'submit' => get_string('submit', 'local_offthejob'),
    'userid' => $_GET['userid'],
    'courseid' => $_GET['courseid'],
    'username' => $username->username,
    'coursename' => $coursename->fullname,
    'date_val' => $date,
    'activity_val' => $activity,
    'whatlink_val_opt' => array_values($modulesarray),
    'whatlink_val' => $whatlink,
    'impact_val' => $impact,
    'duration_val' => $duration,
    'initial_val' => $initial,
];
echo $OUTPUT->render_from_template('local_offthejob/hours_table', $template);

if($error == false){
    echo "<h2 class='text-success bold'>$string</h2>";
} elseif($error == true){
    echo "<h2 class='text-danger bold'>$string</h2>";
}

$hoursarray = $lib->all_hours($_GET['userid'], $_GET['courseid']);
$tabletemplate = (object)[
    'title' => get_string('otjh_table', 'local_offthejob'),
    'date' => get_string('date', 'local_offthejob'),
    'activity' => get_string('activity', 'local_offthejob'),
    'what_unit' => get_string('what_unit', 'local_offthejob'),
    'duration' => get_string('duration', 'local_offthejob'),
    'initial' => get_string('initial', 'local_offthejob'),
    'array' => array_values($hoursarray),
    'update_r' => get_string('update_record', 'local_offthejob'),
    'delete_r' => get_string('delete_record', 'local_offthejob'),
    'total' => count($hoursarray),
    'userid' => $_GET['userid'],
    'courseid' => $_GET['courseid'],
    'print' => get_string('print_t', 'local_offthejob'),
    'id' => get_string('id', 'local_offthejob'),
    'reset' => get_string('reset', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/hours_full_table', $tabletemplate);

$userid = $_GET['userid'];
$courseid = $_GET['courseid'];
$percent = $lib->get_percent_hours($userid, $courseid);
$expected = $lib->get_percent_expect($userid, $courseid);
$otj_info = $lib->otj_info($userid, $courseid);

$infotemplate = (object)[
    'otj_info' => get_string('otj_info', 'local_offthejob'),
    'total_nt' => get_string('total_nt', 'local_offthejob'),
    'total_nl' => get_string('total_nl', 'local_offthejob'),
    'contract_pw' => get_string('contract_pw', 'local_offthejob'),
    'otj_hours_pw' => get_string('otj_hours_pw', 'local_offthejob'),
    'm_on_p' => get_string('m_on_p', 'local_offthejob'),
    'w_on_p' => get_string('w_on_p', 'local_offthejob'),
    'alw' => get_string('alw', 'local_offthejob'),
    'percent' => $percent,
    'expected' => $expected,
    'array' => array_values(array($otj_info)),
    'prog' => get_string('prog', 'local_offthejob'),
    'expect' => get_string('expect', 'local_offthejob'),
    'incomp' => get_string('incomp', 'local_offthejob'),
    'info_t' => get_string('info_t', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/hours_info', $infotemplate);

echo $OUTPUT->footer();

if(!isset($_GET['input'])){
    \local_offthejob\event\viewed_hours::create(array('context' => $context, 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
}