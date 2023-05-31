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

$user = $lib->get_current_user();
$username = $lib->get_username($user[0]);
$courseid = $_GET['courseid'];
if(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
    header('Location: ./learner.php');
    exit();
}
$context = context_course::instance($courseid);
require_capability('local/offthejob:student', $context);
$coursename = $lib->get_coursename($courseid);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/otj_doc_learn.php'));
$PAGE->set_title(get_string('otjd', 'local_offthejob').' - '.$username->username .' - Documents');
$PAGE->set_heading(get_string('otjd', 'local_offthejob').' - '.$username->username.' - Documents');
$PAGE->set_pagelayout($courseid);
try{
    $PAGE->set_course($lib->get_course_record($courseid));
} catch(Exception $e){
    header('Location: ./learner.php');
    exit();
}

echo $OUTPUT->header();

$dates = $lib->records_date_learn($courseid);
//Section for documents
$hidden1 = '';
$docs = get_string('docs', 'local_offthejob');
$form = false;
$hiddenback = 'hidden';
if($_GET['form'] == 'true'){
    $form = true;
    $hidden1 = 'hidden';
    $docs = '';
    $dates = [];
    $draft = '';
    $editdraft = '';
    $hidden0 = 'hidden';
    $hiddenback = '';
}

//Top of Page
$martemplate = (object)[
    'mar' => get_string('mar', 'local_offthejob'),
    'btm' => get_string('btm', 'local_offthejob'),
    'courseid' => $_GET['courseid'],
    'hidden' => $hidden0,
    'newdoc' => get_string('newdoc', 'local_offthejob'),
    'hidden1' => $hidden1,
    'docs' => $docs,
    'array' => array_values($dates),
    'bta' => get_string('bta', 'local_offthejob'),
    'hiddenback' => $hiddenback
];
echo $OUTPUT->render_from_template('local_offthejob/marhead_learn', $martemplate);

//Array for default values for form
$setup = $lib->setup_data_learn($courseid);
$cumulative = $lib->get_cumulative_hours_learn($courseid);
$percent = $lib->get_percent_hours_learn($courseid);

$array = ["$username->username",'',"$coursename->fullname","$setup->employerorstore","$setup->coach","$setup->managerormentor","$percent","$cumulative",'','','','','','','','','','','','','','','','','','','','','',''];
$error;
$error2 = false;
//Check for type
if($_GET['type'] == 'error'){
    $string = 'Invalid: <br>';
    $total = $_GET['total'];
    $int = 0;
    while($int < $total){
        $learnt= get_string('learn_t', 'local_offthejob');
        $targetn = get_string('target_next', 'local_offthejob');
        $input = $_GET["input$int"];
        if ($input == 'apprencom'){
            $string = "$string".get_string('apprentice_comment', 'local_offthejob')."<br>";
        } 
        $int++;
    }
    $error = true;
} elseif ($_GET['type'] == 'success'){
    $array = $lib->get_doc_id_learn($_GET['id'], $courseid);
    $error = false;
} elseif ($_GET['type'] == 'update'){
    $array = $lib->get_doc_id_learn($_GET['id'], $courseid);
    $error2 = true;
}

//Check error value
if($error !== null && $error2 == false){
    if($error == true){
        echo"<h2 class='text-danger bold'>$string</h2>";
        $array = $lib->get_draft_learn($_GET['courseid']);
    } elseif ($error == false){
        echo"<h2 class='bold'>Success</h2>";
    }
}

if($form == true){
    //Get signuatures related to document
    $learnsign = $lib->learnsign($courseid);
    $ntasign = $lib->ntasign_learn($courseid);
    if($array[26] == null || empty($array[26])){
        $hidden = '';
        $hidden1 = 'hidden';
    } else {
        $hidden = 'hidden';
        $hidden1 = '';
    }
    if($array[25] == null || empty($array[25])){
        $hidden3 = 'hidden';
    } else {
        $hidden3 = '';
    }
    if(!empty($array[23])){
        $pdfurl = $array[23];
        $array[23] = "./classes/pdf/tmp/tmp_learn.php?file=$array[23]&courseid=$courseid";
    }
    //Add in mustache file for form
    $emptytemplate = (object)[
        'apprentice' => get_string('apprentice', 'local_offthejob'),
        'reviewd' => get_string('reviewd', 'local_offthejob'),
        'standard' => get_string('standard', 'local_offthejob'),
        'eands' => get_string('eands', 'local_offthejob'),
        'coach' => get_string('coach', 'local_offthejob'),
        'morm' => get_string('morm', 'local_offthejob'),
        'sop' => get_string('sop', 'local_offthejob'),
        'progs' => get_string('prog_stat', 'local_offthejob'),
        'percent_prog' => get_string('percent_prog', 'local_offthejob'),
        'otj_hours' => get_string('otj_hours', 'local_offthejob'),
        'behind_t' => get_string('behind_t', 'local_offthejob'),
        'slight_b' => get_string('slight_b', 'local_offthejob'),
        'on_t' => get_string('on_t', 'local_offthejob'),
        'ahead_t' => get_string('ahead_t', 'local_offthejob'),
        'ready_epa' => get_string('ready_epa', 'local_offthejob'),
        'recap_act' => get_string('recap_act', 'local_offthejob'),
        'impactu' => get_string('impact_u', 'local_offthejob'),
        'detail_tal' => get_string('detail_tal', 'local_offthejob'),
        'mod_ksb' => get_string('mod_ksb', 'local_offthejob'),
        'impact_w' => get_string('impact_w', 'local_offthejob'),
        'fsp' => get_string('fsp', 'local_offthejob'),
        'learn_t' => get_string('learn_t', 'local_offthejob'),
        'target_next' => get_string('target_next', 'local_offthejob'),
        'math' => get_string('math', 'local_offthejob'),
        'reading' => get_string('reading', 'local_offthejob'),
        'writing' => get_string('writing', 'local_offthejob'),
        'sandl' => get_string('sandl', 'local_offthejob'),
        'ict' => get_string('ict', 'local_offthejob'),
        'act_sum' => get_string('act_sum', 'local_offthejob'),
        'agreed_act' => get_string('agreed_act', 'local_offthejob'),
        'employer_comment' => get_string('employer_comment', 'local_offthejob'),
        'safeguarding' => get_string('safeguarding', 'local_offthejob'),
        'apprentice_comment' => get_string('apprentice_comment', 'local_offthejob'),
        'learner_sign' => get_string('learner_sign', 'local_offthejob'),
        'submit' => get_string('submit', 'local_offthejob'),
        'date' => get_string('date', 'local_offthejob'),
        'courseid' => $_GET['courseid'],
        'nta_sign' => get_string('nta_sign', 'local_offthejob'),
        'array' => array_values(array($array)),
        'learnsign' => $learnsign,
        'hidden' => $hidden,
        'hidden1' => $hidden1,
        'hidden3' => $hidden3,
        'ntasign' => $ntasign,
        'id' => $_GET['id']
    ];
    echo $OUTPUT->render_from_template('local_offthejob/marlearn', $emptytemplate);
}

echo $OUTPUT->footer();

if(isset($_GET['form'])){
    if($_GET['form'] != 'true'){
        \local_offthejob\event\viewed_mar_page_learner::create(array('context' => $context, 'relateduserid' => $user[0], 'courseid' => $courseid))->trigger();
    }
} else {
    \local_offthejob\event\viewed_mar_page_learner::create(array('context' => $context, 'relateduserid' => $user[0], 'courseid' => $courseid))->trigger();
}