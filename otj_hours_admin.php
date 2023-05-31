<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

//Used for admin hours logs page
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
}

if(!empty($_SESSION['info'])){
    $userid = $_SESSION['info'][0];
    $courseid = $_SESSION['info'][1];
}

if(!preg_match("/^[0-9]*$/", $userid) || 
    empty($userid) || 
    !preg_match("/^[0-9]*$/", $courseid) || 
    empty($courseid)) {
    header("Location: ./admin.php");
} elseif(){
    header("Location: ./admin.php");
}

$username = $lib->get_username($userid);
$coursename = $lib->get_coursename($courseid);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/otj_hours_admin.php'));
$PAGE->set_title('Off The Job - Hours Logs - '.$username->username.' - '.$coursename->fullname);
$PAGE->set_heading('Off The Job - Hours Logs - '.$username->username.' - '.$coursename->fullname);

echo $OUTPUT->header();

$template = (object)[
    'title' => get_string('otjh', 'local_offthejob'),
    'lname' => get_string('learner_name','local_offthejob'),
    'qual' => get_string('qual', 'local_offthejob'),
    'username' => $username->username,
    'coursename' => $coursename->fullname,
    'btm' => get_string('btm', 'local_offthejob'),
];

echo $OUTPUT->render_from_template('local_offthejob/admin_hours_head', $template);

if($_SESSION['info'] !== null){
    $info = $_SESSION['info'];
    $array = $_SESSION['array'];
    $modulesarray = $lib->plan_json_modules($info[0], $info[1]);
    $num = 0;
    foreach($modulesarray as $modarr){
        if($modarr[0] == $array[2]){
            $modulesarray[$num][1] = 'selected';
        }
        $num++;
    }
    $template = (object)[
        'date_val' => date('Y-m-d',$array[0]),
        'activity_val' => $array[1],
        'whatlink_val' => $array[2],
        'impact_val' => $array[3],
        'duration_val' => $array[4],
        'initial_val' => $array[5],
        'userid' => $info[0],
        'courseid' => $info[1],
        'id' => $info[2],
        'whatlink_val_opt' => array_values($modulesarray),
        'update' => get_string('update', 'local_offthejob'),
        'date' => get_string('date', 'local_offthejob'),
        'activity' => get_string('activity', 'local_offthejob'),
        'what_unit' => get_string('what_unit', 'local_offthejob'),
        'how_used' => get_string('how_used', 'local_offthejob'),
        'duration' => get_string('duration', 'local_offthejob'),
        'initial' => get_string('initial', 'local_offthejob'),
        'impact_role' => get_string('impact_role', 'local_offthejob')
    ];
    unset($_SESSION['info']);
    unset($_SESSION['array']);

    $checkarray = $_SESSION['checkarray'];
    unset($_SESSION['checkarray']);
    if($checkarray[0] == 'red'){
        $template->date_red = 'red';
    }
    if($checkarray[1] == 'red'){
        $template->activity_red = 'red';
    }
    if($checkarray[2] == 'red'){
        $template->whatlink_red = 'red';
    }
    if($checkarray[3] == 'red'){
        $template->impact_red = 'red';
    }
    if($checkarray[4] == 'red'){
        $template->duration_red = 'red';
    }
    if($checkarray[5] == 'red'){
        $template->initial_red = 'red';
    }
    if(!empty($checkarray)){
        echo ("<h1 class='bold text-danger text-center'>Invalid inputs are highligheted in red!</h1>");
    } else {
        echo ("<h1 class='bold text-danger text-center'>Click update to update the record</h1>");
    }
    echo $OUTPUT->render_from_template('local_offthejob/admin_hours_edit', $template);
}

if(!empty($_SESSION['success'])){
    if($_SESSION['success'] == 'update'){
        echo("<h1 class='bold text-success text-center'>Update Success</h1>");
    } elseif($_SESSION['success'] == 'delete'){
        echo("<h1 class='bold text-success text-center'>Deletion Success</h1>");
    }
    unset($_SESSION['success']);
}

$template = (object)[
    'date' => get_string('date', 'local_offthejob'),
    'activity' => get_string('activity', 'local_offthejob'),
    'what_unit' => get_string('what_unit', 'local_offthejob'),
    'duration' => get_string('duration', 'local_offthejob'),
    'initial' => get_string('initial', 'local_offthejob'),
    'add_newr' => get_string('add_newr', 'local_offthejob'),
    'remove_r' => get_string('remove_r', 'local_offthejob'),
    'submit' => get_string('submit', 'local_offthejob'),
    'update_r' => get_string('update_record', 'local_offthejob'),
    'delete_r' => get_string('delete_record', 'local_offthejob'),
    'userid' => $userid,
    'courseid' => $courseid,
    'print' => get_string('print_t', 'local_offthejob'),
    'id' => get_string('id', 'local_offthejob'),
    'reset' => get_string('reset', 'local_offthejob'),
    'impact_role' => get_string('impact_role', 'local_offthejob')
];
$hours = $lib->all_hours($userid, $courseid);
$template->array = array_values($hours);
$template->total = count($hours);
echo $OUTPUT->render_from_template('local_offthejob/admin_hours_table', $template);

echo $OUTPUT->footer();