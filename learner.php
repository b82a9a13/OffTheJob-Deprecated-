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

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/learner.php'));
$PAGE->set_title(get_string('otjt_title', 'local_offthejob'));
$PAGE->set_heading(get_string('otjt_title', 'local_offthejob'));

echo $OUTPUT->header();

$apprentice = $lib->learner_enrol();
$templatecontext = (object)[
    'title' => get_string('otjt_menu', 'local_offthejob'),
    'array' => array_values(array($array)),
    'courses' => array_values($apprentice),
    'desc' => get_string('otjt_menu_desc', 'local_offthejob'),
    'instruct' => get_string('otjt_menu_instruct', 'local_offthejob'),
    'hi' => get_string('hi', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/learner', $templatecontext);

//Add hidden to array dependant on if the intial setup record exists
$array = [];
foreach($apprentice as $appr){
    $exists = $lib->setup_exists_learn($appr[2]);
    if($exists == false){
        array_push($array, [$appr[0], $appr[1], $appr[2], 'hidden', '', 'hidden']);
    } elseif ($exists == true){
        $learnsign = $lib->learn_sign_exists($appr[2]);
        if($learnsign == true){
            $percent = $lib->get_percent_hours_learn($appr[2]);
            $expected = $lib->get_percent_expect_learn($appr[2]);
            $complete = $lib->assign_comp_learn($appr[2]);
            array_push($array, [$appr[0], $appr[1], $appr[2], '', 'hidden', 'hidden', $percent, $expected, $complete[0], $complete[1]]);
        } elseif ($learnsign == false){
            array_push($array, [$appr[0], $appr[1], $appr[2], 'hidden', 'hidden', '']);
        }
    }
}
$apprentice = $array;

$apprenttemplate = (object)[
    'courses' => array_values($apprentice),
    'mar' => get_string('mar', 'local_offthejob'),
    'hour_log' => get_string('hour_log', 'local_offthejob'),
    'setup_contact' => get_string('setup_contact', 'local_offthejob'),
    'prog' => get_string('prog', 'local_offthejob'),
    'expect' => get_string('expect', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/learnermenu', $apprenttemplate);

?>
<style>
    .inner-div-learner-table, .outer-div-learner-table{
        border: 2px solid #95287A;
        border-radius: 5px;
        padding: .5rem;
        margin-bottom: .75rem;
        margin-right: .5rem;
        display: inline-block;
    }
    .inner-div-learner-table{
        background: #FCFCFC;
    }
    .outer-div-learner-table{
        background: #F5F5F5;
    }
</style>
<?php

echo $OUTPUT->footer();

\local_offthejob\event\viewed_learner_page::create(array('context' => \context_system::instance()))->trigger();