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

$courseid = $_GET['courseid'];
if(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)) {
    header("Location: ./learner.php");
    exit();
}
$context = context_course::instance($courseid);
require_capability('local/offthejob:student', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/learnsign.php'));
$PAGE->set_title('Off the Job - Create Signature'); 
$PAGE->set_heading('Off the Job - Create Signature');
$PAGE->set_pagelayout('incourse');
try{
    $PAGE->set_course($lib->get_course_record($courseid));
} catch(Exception $e){
    header("Location: ./learner.php");
    exit();
}

echo $OUTPUT->header();

$template = (object)[
    'clear' => get_string('clear', 'local_offthejob'),
    'submit' => get_string('submit', 'local_offthejob'),
    'courseid' => $courseid,
    'confirm' => get_string('confirm', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/learnsign', $template);

echo $OUTPUT->footer();
echo("<script src='./classes/js/sign.js'></script>");