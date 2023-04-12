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


$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/teacher.php'));
$PAGE->set_title(get_string('otjt_titlec', 'local_offthejob'));
$PAGE->set_heading(get_string('otjt_titlec', 'local_offthejob'));

echo $OUTPUT->header();

$username = $lib->get_current_user();
$username = $username[1];
$enrolments = $lib->get_enrolments();
$teachertemplate = (object)[
    'title' => get_string('otjt_menu', 'local_offthejob'),
    'user' => $username,
    'hi' => get_string('hi', 'local_offthejob'),
    'desc' => get_string('otjt_menu_desc_teach', 'local_offthejob'),
    'instruct' => get_string('otjt_menu_instruct_teach', 'local_offthejob'),
    'courses' => array_values($enrolments),
    'users' => get_string('users', 'local_offthejob'),
    'show' => get_string('show', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/teacher', $teachertemplate);

if(!empty($_GET['setup'])){
    echo("<h2 class='bold text-success'>Setup Success</h2>");
}
if(!empty($_GET['sign'])){
    $sign = $_GET['sign'];
    if($sign === 'true'){
        echo("<h2 class='bold text-success'>Signature Creation Success</h2>");
    } elseif($sign === 'false'){
        echo("<h2 class='bold text-danger'>Signature Creation Failed!</h2>");
    }
}

//Section for working out if the setup has been done
$false = [];
foreach($enrolments as $enrolment){
    $teacherc = $lib->teacher_course_users($enrolment[2]);
    foreach($teacherc as $teach){
        $boolean = $lib->setup_exists($teach[0], $teach[3]);
        //(userid, courseid, fullname, coursename, setup_existence)
        if($boolean == false){
            array_push($false, [$teach[0], $teach[3], $teach[1], $teach[2], 'false']);
        } elseif($boolean == true) {
            array_push($false, [$teach[0], $teach[3], $teach[1], $teach[2], 'true']);
        }
    }
}
//If a setup hasn't been complete change value of array
$finalarray = [];
foreach($false as $fal){
    if($fal[4] == 'true'){
        $percent = $lib->get_percent_hours($fal[0], $fal[1]);
        $expected = $lib->get_percent_expect($fal[0], $fal[1]);
        $complete = $lib->assign_comp($fal[0], $fal[1]);
        if($lib->check_teach_setup($fal[0], $fal[1]) === true){
            array_push($finalarray, [$fal[0], $fal[2], $fal[3], $fal[1], 'hidden', '', $percent, $expected, $complete[0], $complete[1], '']);
        } else {
            array_push($finalarray, [$fal[0], $fal[2], $fal[3], $fal[1], 'hidden', 'hidden', $percent, $expected, $complete[0], $complete[1], 'hidden']);
        }
    } elseif($fal[4] == 'false'){
        array_push($finalarray, [$fal[0], $fal[2], $fal[3], $fal[1], '', 'hidden', '']);
    }
}

//Button selection of users documents
$inter = 0;
foreach($enrolments as $enrolment){
    echo("<div id='coursediv$enrolment[2]' hidden>");
        //Only get revelant data for course
        $array = [];
        foreach($finalarray as $finarray){
            if($finarray[3] == $enrolment[2]){
                //check if signature needs to be created
                if($lib->teach_check_signature($finarray[0], $finarray[3]) === true){
                    if(!isset($finarray[7])){
                        $value7 = '';
                    } else {
                        $value7 = $finarray[7];
                    }
                    if(!isset($finarray[8])){
                        $value8 = '';
                    } else {
                        $value8 = $finarray[8];
                    }
                    if(!isset($finarray[9])){
                        $value9 = '';
                    } else {
                        $value9 = $finarray[9];
                    }
                    if(!isset($finarray[10])){
                        $value10 = '';
                    } else {
                        $value10 = $finarray[10];
                    }
                    array_push($array, [$finarray[1], $finarray[0], $finarray[2], $finarray[3], $finarray[4], $finarray[5], $finarray[6], $value7, $value8, $value9, 'hidden', $value10]);
                } else {
                    array_push($array, [$finarray[1], $finarray[0], $finarray[2], $finarray[3], 'hidden', 'hidden', $finarray[6], $finarray[7], $finarray[8], $finarray[9], '', '']);
                }
            }
        }
        asort($array);
        $lfctemplate = (object)[
            'course' => $enrolment[0],
            'array' => array_values($array),
            'subheading' => get_string('user_docs_btn', 'local_offthejob'),
            'title' => get_string('user_docs_title', 'local_offthejob'),
            'mad' => get_string('mad', 'local_offthejob'),
            'otjhl'  => get_string('otjhl', 'local_offthejob'),
            'setup' => get_string('setup', 'local_offthejob'),
            'int' => $inter,
            'expect' => get_string('expect', 'local_offthejob'),
            'prog' => get_string('prog', 'local_offthejob')
        ];
        echo $OUTPUT->render_from_template('local_offthejob/learnerforcourse', $lfctemplate);
        $inter++;
    echo("</div>");
}
?>
<!--- Script for button click ---->
<script>
    //Event for when the course button is clicked
    function course_click($int){
        if(document.getElementById('coursediv'+$int).hidden == true){
            document.getElementById('coursediv'+$int).hidden = false;
            document.getElementById('btn'+$int).className = 'btn-secondary mb-2 mr-2 p-2';
        } else {
            document.getElementById('coursediv'+$int).hidden = true;
            document.getElementById('btn'+$int).className = 'btn-primary mb-2 mr-2 p-2';
        }
    }
</script>
<style>
    .box-default{
        cursor: pointer;
    }
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

\local_offthejob\event\viewed_teacher_page::create(array('context' => \context_system::instance()))->trigger();
?>