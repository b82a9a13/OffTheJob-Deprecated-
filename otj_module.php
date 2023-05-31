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
$lib = new lib();

$userid = $_GET['userid'];
$courseid = $_GET['courseid'];
if(!preg_match("/^[0-9]*$/", $userid) || 
    empty($userid) || 
    !preg_match("/^[0-9]*$/", $courseid) || 
    empty($courseid)) {
    header('Location: ./teacher.php');
    exit();
}
$context = context_course::instance($courseid);
require_capability('local/offthejob:teacher', $context);
$username = $lib->get_username($userid);
$coursename = $lib->get_coursename($courseid);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/otj_module.php'));
$PAGE->set_title('Module Completion - '.$username->username.' - '.$coursename->fullname);
$PAGE->set_heading('Module Completion - '.$username->username.' - '.$coursename->fullname);
$PAGE->set_pagelayout('incourse');
try{
    $PAGE->set_course($lib->get_course_record($courseid));
} catch(Exception $e){
    header('Location: ./teacher.php');
    exit();
}
echo $OUTPUT->header();

$btmtemplate = (Object)[
    'btm' => get_string('btm', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/btm', $btmtemplate);

//Create and output header for module page
$complete = $lib->assign_comp($userid, $courseid);
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
$modinfo = $lib->module_table($userid, $courseid);
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

\local_offthejob\event\viewed_module_completion::create(array("context" => $context, 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();