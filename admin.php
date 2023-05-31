<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

//Used for admin page
require_once(__DIR__.'/../../config.php');

require_login();

$context = context_system::instance();
require_capability('local/offthejob:manager', $context);

use local_offthejob\lib;
$lib = new lib();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/admin.php'));
$PAGE->set_title('Off The Job - Admin');
$PAGE->set_heading('Off The Job - Admin');

echo $OUTPUT->header();

$titletemplate = (Object)[
    'reset_p' => get_string('reset_p', 'local_offthejob'),
    'admin' => get_string('admin', 'local_offthejob'),
    'otj' => get_string('otj', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/admin_title', $titletemplate);

if(isset($_SESSION['success'])){
    $success = $_SESSION['success'];
    if($success === 'learn-sign'){
        echo("<h2 class='bold text-danger'>Learner Signature Deleted</h2>");
    } elseif($success === 'nta-sign'){
        echo("<h2 class='bold text-danger'>NTA Signature Deleted</h2>");
    } elseif($success === 'trainplandel'){
        echo("<h2 class='bold text-danger'>Training Plan Deleted</h2>");
    } elseif($success === 'initialdel'){
        echo("<h2 class='bold text-danger'>Initial Setup Deleted</h2>");
    } elseif($success === 'docdeletion'){
        echo("<h2 class='bold text-danger'>Monthly Activity Document Deleted</h2>");
    } elseif($success === 'draftdeletion'){
        echo("<h2 class='bold text-danger'>Monthly Activity Draft Deleted</h2>");
    } elseif($success === 'trainplan'){
        echo("<h2 class='bold text-success'>Training Plan Updated</h2>");
    } elseif($success === 'initial-edit'){
        echo("<h2 class='bold text-success'>Initial Setup Updated</h2>");
    } elseif($success === 'draftsuccess'){
        echo("<h2 class='bold text-success'>Monthly Activity Draft Completed</h2>");
    } elseif($success === 'docupdate'){
        echo("<h2 class='bold text-success'>Monthly Activity Document Updated</h2>");
    }
    unset($_SESSION['success']);
}

$editPage = false;
$userid = null;
$courseid = null;
//Check if submit post is set
if(!isset($_POST['submit'])){
    //Add in option to select user and course and a button for admin reports
    $setuprecords = $lib->setted_users();
    $template = (Object)[
        'setuparray' => $setuprecords
    ];
    echo $OUTPUT->render_from_template('local_offthejob/admin_select', $template);
} elseif(isset($_POST['submit'])){
    //If option is not selected
    if($_POST['opt'] == null){
        $setuprecords = $lib->setted_users();
        $template = (Object)[
            'setuparray' => $setuprecords
        ];
        $template->error = 'Please Select a User and Course';
        echo $OUTPUT->render_from_template('local_offthejob/admin_select', $template);
    //Else carry on
    } else {
        $editPage = true;
        //Get data and create variables
        $opt = $_POST['opt'];
        $values = explode("-", $opt);
        $userid = $values[0];
        $courseid = $values[1];
        $username = $lib->get_username($values[0])->username;
        $coursename = $lib->get_coursename($values[1])->fullname;
        $signatures = $lib->admin_signatures($values[0], $values[1]);
        $initial = $lib->admin_initial($values[0], $values[1]);
        $trainplan = $lib->admin_plan_data($values[0], $values[1]);
        $startdate = $trainplan->startdate;
        if($startdate !== null){
            $startdate = date('d-m-Y', $startdate);
        } else {
            $startdate = null;
        }
        $template = (Object)[
            'username' => $username,
            'coursename' => $coursename,
            'userid' => $userid,
            'courseid' => $courseid,
            'learnsign' => $signatures->learnersign,
            'ntasign' => $signatures->ntasign,
            'totalmonths' => $initial->totalmonths,
            'otjhours' => $initial->otjhours,
            'employerorstore' => $initial->employerorstore,
            'coach' => $initial->coach,
            'mom' => $initial->managerormentor,
            'startdate' => date('d-m-Y',$initial->startdate),
            'hoursperweek' => $initial->hoursperweek,
            'annuallw' => $initial->annuallw,
            'planfilename' => $initial->planfilename,
            'plan_employer' => $trainplan->employer,
            'plan_name' => $trainplan->name,
            'plan_startdate' => $startdate,
            'plan_plannedendd' => $trainplan->plannedendd,
            'plan_otjh' => $trainplan->otjh,
            'plan_epao' => $trainplan->epao,
            'plan_fundsource' => $trainplan->fundsource,
            'plan_lengthoprog' => $trainplan->lengthoprog,
            'reset' => get_string('reset', 'local_offthejob'),
            'edit' => get_string('edit', 'local_offthejob'),
            'view' => get_string('view', 'local_offthejob'),
            'select_todo' => get_string('select_todo', 'local_offthejob'),
            'initial_s' => get_string('initial_s', 'local_offthejob'),
            'learner_sign' => get_string('learner_sign', 'local_offthejob'),
            'nta_sign' => get_string('nta_sign', 'local_offthejob'),
            'train_p' => get_string('train_p', 'local_offthejob'),
            'activity_r' => get_string('activity_r', 'local_offthejob'),
            'hours_log' => get_string('hours_log', 'local_offthejob'),
            'close' => get_string('close', 'local_offthejob'),
            'total_m' => get_string('total_m', 'local_offthejob'),
            'otjh' => get_string('otjh', 'local_offthejob'),
            'eands' => get_string('eands', 'local_offthejob'),
            'coach' => get_string('coach', 'local_offthejob'),
            'morm' => get_string('morm', 'local_offthejob'),
            'startdate' => get_string('startdate', 'local_offthejob'),
            'contract_pw' => get_string('contract_pw', 'local_offthejob'),
            'alw' => get_string('alw', 'local_offthejob'),
            'reset_initial' => get_string('reset_initial', 'local_offthejob'),
            'yes' => get_string('yes', 'local_offthejob'),
            'no' => get_string('no', 'local_offthejob'),
            'reset_sign' => get_string('reset_sign', 'local_offthejob'),
            'name' => get_string('name', 'local_offthejob'),
            'employer' => get_string('employer', 'local_offthejob'),
            'fsped' => get_string('fsped', 'local_offthejob'),
            'lop' => get_string('lop', 'local_offthejob'),
            'cap_otjh' => get_string('cap_otjh', 'local_offthejob'),
            'epao' => get_string('epao', 'local_offthejob'),
            'fund_source' => get_string('fund_source', 'local_offthejob'),
            'reset_plan' => get_string('reset_plan', 'local_offthejob')
        ];
        //Checks if the data linked to the buttons exists
        if($lib->plan_exists($values[0], $values[1]) == false){
            $template->plan_disable = 'disabled';
        }
        if($lib->setup_exists($values[0], $values[1]) == false){
            $template->initial_disable = 'disabled';
        }
        if($lib->admin_learn_sign_exists($values[0], $values[1]) == false){
            $template->learner_disable = 'disabled';
        }
        if($lib->nta_sign_exists($values[0], $values[1]) == false){
            $template->nta_disable = 'disabled';
        }
        echo $OUTPUT->render_from_template('local_offthejob/admin_edit', $template);
    }
}

echo $OUTPUT->footer();

if($editPage){
    \local_offthejob\event\viewed_admin_edit_menu::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
} else {
    \local_offthejob\event\viewed_admin_menu::create(array('context' => $context))->trigger();
}