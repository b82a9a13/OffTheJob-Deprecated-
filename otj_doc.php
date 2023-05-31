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
$PAGE->set_url(new moodle_url('/local/offthejob/otj_doc.php'));
$PAGE->set_title(get_string('otjd', 'local_offthejob').' - '.$username->username);
$PAGE->set_heading(get_string('otjd', 'local_offthejob').' - '.$username->username);
$PAGE->set_pagelayout('incourse');
try{
    $PAGE->set_course($lib->get_course_record($courseid));
} catch(Exception $e){
    header('Location: ./teacher.php');
    exit();
}

echo $OUTPUT->header();

//check if record exists
$existence = $lib->docs_exists($userid, $courseid);



//Section for draft
$hidden0 = 'hidden';
$userid = $userid;
$courseid = $courseid;
if($lib->draft_exists($userid, $courseid) == true){
    $draft = get_string('draft', 'local_offthejob');
    $editdraft = get_string('draftedit', 'local_offthejob');
    $hidden0 = '';
}
$dates = $lib->records_date($userid, $courseid);

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
    'userid' => $userid,
    'courseid' => $courseid,
    'draft' => $draft,
    'editdraft' => $editdraft,
    'hidden' => $hidden0,
    'newdoc' => get_string('newdoc', 'local_offthejob'),
    'hidden1' => $hidden1,
    'docs' => $docs,
    'array' => array_values($dates),
    'bta' => get_string('bta', 'local_offthejob'),
    'hiddenback' => $hiddenback
];
echo $OUTPUT->render_from_template('local_offthejob/marhead', $martemplate);

//Array for default values for form
$setup = $lib->setup_data($userid, $courseid);
$cumulative = $lib->get_cumulative_hours($userid, $courseid);
$percent = $lib->get_percent_hours($userid, $courseid);
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
        if($input == 'apprentice'){
            $string = "$string".get_string('apprentice', 'local_offthejob')."<br>";
        } elseif ($input == 'reviewdate'){
            $string = "$string".get_string('reviewd', 'local_offthejob')."<br>";
        } elseif ($input == 'standard'){
            $string = "$string".get_string('standard', 'local_offthejob')."<br>";
        } elseif ($input == 'eands'){
            $string = "$string".get_string('eands', 'local_offthejob')."<br>";
        } elseif ($input == 'mom'){
            $string = "$string".get_string('morm', 'local_offthejob')."<br>";
        } elseif ($input == 'progress'){
            $string = "$string".get_string('percent_prog', 'local_offthejob')."<br>";
        } elseif ($input == 'hours'){
            $string = "$string".get_string('otjh', 'local_offthejob')."<br>";
        } elseif ($input == 'progstat'){
            $string = "$string".get_string('prog_stat', 'local_offthejob')."<br>";
        } elseif ($input == 'recap'){
            $string = "$string".get_string('recap_act', 'local_offthejob')."<br>";
        } elseif ($input == 'impact'){
            $string = "$string".get_string('impact_u', 'local_offthejob')."<br>";
        } elseif ($input == 'details'){
            $string = "$string".get_string('detail_tal', 'local_offthejob')."<br>";
        } elseif ($input == 'detailsksb'){
            $string = "$string".get_string('detail_mod', 'local_offthejob')."<br>";
        } elseif ($input == 'detailimpact'){
            $string = "$string ".get_string('impact_w', 'local_offthejob')." <br>";
        } elseif ($input == 'tmath'){
            $string = "$string".get_string('math', 'local_offthejob')." - ".$learnt."<br>";
        } elseif ($input == 'nmath'){  
            $string = "$string".get_string('math', 'local_offthejob')." - ".$targetn."<br>";
        } elseif ($input == 'tict'){
            $string = "$string".get_string('ict', 'local_offthejob')." - ".$learnt."<br>";
        } elseif ($input == 'nict'){
            $string = "$string".get_string('ict', 'local_offthejob')." - ".$targetn." <br>";
        } elseif ($input == 'activity'){
            $string = "$string".get_string('act_sum', 'local_offthejob')."<br>";
        } elseif ($input == 'activityksb'){
            $string = "$string".get_string('off_act_mod', 'local_offthejob')."<br>";
        } elseif ($input == 'aaction'){
            $string = "$string".get_string('agreed_act', 'local_offthejob')."<br>";
        } elseif ($input == 'file'){
            $string = "$string"."Invalid employer comment pdf<br>";
        } elseif ($input == 'safeguard'){
            $string = "$string".get_string('safeguarding', 'local_offthejob')."<br>";
        } elseif ($input == 'apprencom'){
            $string = "$string".get_string('apprentice_comment', 'local_offthejob')."<br>";
        } elseif ($input == 'otjhexpected'){
            $string = "$string % Expected Progress according to Training Plan <br>";
        } elseif ($input == 'progexpected'){
            $string = "$string Expected OTJH as per Training Plan <br>";
        } elseif ($input == 'progcom'){
            $string = "$string Progress Comment <br>";
        } elseif ($input == 'otjhcom'){
            $string = "$string OTJH Comment <br>";
        } elseif ($input == 'alnsupport'){
            $string = "$string ALN Support delivered today <br>";
        }
        $int++;
    }
    $error = true;
} elseif ($_GET['type'] == 'success'){
    $error = false;
} elseif ($_GET['type'] == 'update'){
    $array = $lib->get_doc_id($_GET['id']);
    $lib->del_doc_id($_GET['id']);
    $error2 = true;
    echo"<h2 class='bold text-danger'>The record selected for update has been deleted from the database and will be added back once submitted</h2>";
} elseif ($_GET['type'] == 'delete'){
    echo"<h2 class='bold text-danger'>Deletion Success</h2>";
}

//Check error value
if($error !== null && $error2 == false){
    if($error == true){
        echo"<h2 class='text-danger bold'>$string</h2>";
        $array = $lib->get_draft($userid, $courseid);
    } elseif ($error == false){
        echo"<h2 class='bold text-success'>Success</h2>";
    }
}

if($form == true){
    $signstring = $lib->ntasign($userid, $courseid);
    $learnstring = $lib->learnsigned($userid, $courseid);
    if($array[25] == null || empty($array[25])){
        $hidden = '';
        $hidden1 = 'hidden';
    } else {
        $hidden = 'hidden';
        $hidden1 = '';
    }
    if($array[26] == null || empty($array[26])){
        $hidden3 = 'hidden';
    } else {
        $hidden3 = '';
    }
    if(!empty($array[23])){
        $pdfurl = $array[23];
        $array[23] = "./classes/pdf/tmp/tmp.php?file=$array[23]&userid=$userid&courseid=$courseid";
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
        'userid' => $userid,
        'courseid' => $courseid,
        'nta_sign' => get_string('nta_sign', 'local_offthejob'),
        'array' => array_values(array($array)),
        'ntasig' => $signstring,
        'hidden' => $hidden,
        'hidden1' => $hidden1,
        'hidden3' => $hidden3,
        'learnsig' => $learnstring,
        'pdfurl' => $pdfurl
    ];
    echo $OUTPUT->render_from_template('local_offthejob/mar', $emptytemplate);
}

echo $OUTPUT->footer();

if(isset($_GET['form'])){
    if($_GET['form'] != 'true'){
        \local_offthejob\event\viewed_mar_page::create(array('context' => $context, 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
    }
} else if(!isset($_GET['type'])){
    \local_offthejob\event\viewed_mar_page::create(array('context' => $context, 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
}