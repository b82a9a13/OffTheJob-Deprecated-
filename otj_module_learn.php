<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

//Used for learner to edit their own off the job
require_once(__DIR__.'/../../config.php');

use local_offthejob\lib;

require_login();
$lib = new lib();

$array = $lib->learner_enrol();
$context = context_course::instance($array[0][2]);
require_capability('local/offthejob:student', $context);

$array = $lib->get_current_user();
$username = $lib->get_username($array[0]);
$courseid = $_GET['courseid'];
if(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
    header("Location: ./learner.php");
    exit();
}
$coursename = $lib->get_coursename($courseid);
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/otj_module_learn.php'));
$PAGE->set_title('Module Completion - '.$username->username.' - '.$coursename->fullname);
$PAGE->set_heading('Module Completion - '.$username->username.' - '.$coursename->fullname);

echo $OUTPUT->header();

$temp = (object)[
    'btm' => get_string('btm', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/btm_learn', $temp);

//Create and output header for module page
$complete = $lib->assign_comp_learn($courseid);
$template = (object)[
    'username' => $username->username,
    'coursename' => $coursename->fullname,
    'expected' => $complete[1],
    'percent' => $complete[0],
    'mod_c' => get_string('mod_c', 'local_offthejob'),
    'prog_b' => get_string('prog_b', 'local_offthejob'),
    'prog' => get_string('prog', 'local_offthejob'),
    'expect' => get_string('expect', 'local_offthejob'),
    'incomp' => get_string('incomp', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/module_head', $template);

//Create Module Table
$modinfo = $lib->module_table_learn($courseid);
$array = [];
foreach($modinfo as $minfo){
    if($minfo[2] == 'Complete'){
        array_push($array, [$minfo[0], $minfo[1], $minfo[2], 'green']);
    } elseif ($minfo[2] == 'Incomplete'){
        array_push($array, [$minfo[0], $minfo[1], $minfo[2], 'red']);
    }
}
$modulestemplate = (object)[
    'array' => array_values($array),
    'mod_n' => get_string('mod_n', 'local_offthejob'),
    'mod_t' => get_string('mod_t', 'local_offthejob'),
    'comp_s' => get_string('comp_s', 'local_offthejob'),
    'mod_table' => get_string('mod_table', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/modules_table', $modulestemplate);

echo $OUTPUT->footer();

global $USER;
\local_offthejob\event\viewed_module_completion_learner::create(array("context" => $context, 'relateduserid' => $USER->id, 'courseid' => $courseid))->trigger();