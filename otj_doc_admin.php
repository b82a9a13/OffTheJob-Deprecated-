<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

//Used for admin activity report page
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
if($_SESSION['info'] !== null){
    $userid = $_SESSION['info'][0];
    $courseid = $_SESSION['info'][1];
}

if(!preg_match("/^[0-9]*$/", $userid) || 
    empty($userid) || 
    !preg_match("/^[0-9]*$/", $courseid) || 
    empty($courseid)) {
    header("Location: ./admin.php");
}
$username = $lib->get_username($userid);
$coursename = $lib->get_coursename($courseid);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/otj_doc_admin.php'));
$PAGE->set_title('Off The Job - Activity Reports - '.$username->username.' - '.$coursename->fullname);
$PAGE->set_heading('Off The Job - Activity Reports - '.$username->username.' - '.$coursename->fullname);

echo $OUTPUT->header();

$template = (object)[
    'username' => $username->username,
    'coursename' => $coursename->fullname,
    'userid' => $userid,
    'courseid' => $courseid
];
echo $OUTPUT->render_from_template('local_offthejob/admin_mar_title', $template);

if(empty($_POST['type']) && empty($_SESSION['info'])){
    $template = (object)[
        'userid' => $userid,
        'courseid' => $courseid,
        'draft_hidden' => 'hidden',
        'docs_hidden' => 'hidden',
        'draft' => get_string('draft', 'local_offthejob'),
        'edit' => get_string('edit', 'local_offthejob'),
        'delete' => get_string('delete', 'local_offthejob'),
        'yes' => get_string('yes', 'local_offthejob'),
        'no' => get_string('no', 'local_offthejob'),
        'docs' => get_string('docs', 'local_offthejob'),
    ];
    $draftexists = $lib->admin_draft_exists($userid, $courseid);
    if($draftexists == true){
        $draftid = $lib->admin_draft_id($userid, $courseid);
        $template->draftid = $draftid;
        $template->draft_hidden = '';
    }
    $docsexists = $lib->admin_docs_exists($userid, $courseid);
    if($docsexists == true){
        $array = $lib->admin_docs_ids($userid, $courseid);
        $template->array = array_values($array);
        $template->docs_hidden = '';
    }
    echo $OUTPUT->render_from_template('local_offthejob/admin_mar_head', $template);
} else {
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
        'nta_sign' => get_string('nta_sign', 'local_offthejob'),
        'error_hidden' => 'hidden'
    ];
    $signstring = $lib->ntasign($userid, $courseid);
    $learnstring = $lib->learnsigned($userid, $courseid);
    $emptytemplate->ntasig = $signstring;
    $emptytemplate->learnsig = $learnstring;
    $type = $_POST['type'];
    $id = $_POST['docid'];
    if($type == 'editdoc'){
        $array = $lib->get_doc_id($id);
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
        $emptytemplate->userid = $userid;
        $emptytemplate->courseid = $courseid;
        $tempValue = $array[16];
        $array[16] = $array[17];
        $array[17] = $tempValue;
        $emptytemplate->array = array_values(array($array));
        $emptytemplate->hidden = $hidden;
        $emptytemplate->hidden1 = $hidden1;
        $emptytemplate->hidden3 = $hidden3;
        $emptytemplate->id = $id;
        $emptytemplate->pdfurl = $pdfurl;
    } elseif($_SESSION['info'][3] == 'error'){
        $info = $_SESSION['info'];
        $emptytemplate->userid = $info[0];
        $emptytemplate->courseid = $info[1];
        $emptytemplate->id = $info[2];
        $emptytemplate->error_hidden = '';

        $array = $_SESSION['array'];
        if($array[25] == null || empty($array[25])){
            $hidden = '';
            $hidden1 = 'hidden';
        } else {
            $array[25] = date('Y-m-d', $array[25]);
            $hidden = 'hidden';
            $hidden1 = '';
        }
        if($array[26] == null || empty($array[26])){
            $hidden3 = 'hidden';
        } else {
            $array[26] = date('Y-m-d', $array[26]);
            $hidden3 = '';
        }
        if(!empty($array[23])){
            $pdfurl = $array[23];
            $array[23] = "./classes/pdf/tmp/tmp.php?file=$array[23]&userid=$userid&courseid=$courseid";
        }
        $emptytemplate->pdfurl = $pdfurl;
        $emptytemplate->hidden = $hidden;
        $emptytemplate->hidden1 = $hidden1;
        $emptytemplate->hidden3 = $hidden3;
        $emptytemplate->array = array_values(array($array));

        $checkarray = $_SESSION['checkarray'];
        if($checkarray[0] == 'red'){
            $emptytemplate->apprentice_red = 'red';
        }
        if($checkarray[1] == 'red'){
            $emptytemplate->reviewdate_red = 'red';
        }
        if($checkarray[2] == 'red'){
            $emptytemplate->standard_red = 'red';
        }
        if($checkarray[3] == 'red'){
            $emptytemplate->eands_red = 'red';
        }
        if($checkarray[4] == 'red') {
            $emptytemplate->coach_red = 'red';
        }
        if($checkarray[5] == 'red'){
            $emptytemplate->mom_red = 'red';
        }
        if($checkarray[6] == 'red'){
            $emptytemplate->percent_prog_red = 'red';
        }
        if($checkarray[7] == 'red'){
            $emptytemplate->otj_hours_red = 'red';
        }
        if($checkarray[8] == 'red'){
            $emptytemplate->recap_red = 'red';
        }
        if($checkarray[9] == 'red'){
            $emptytemplate->impact_red = 'red';
        }
        if($checkarray[10] == 'red'){
            $emptytemplate->details_red = 'red';
        }
        if($checkarray[11] == 'red'){
            $emptytemplate->detailsksb_red = 'red';
        }
        if($checkarray[12] == 'red'){
            $emptytemplate->detailimpact_red = 'red';
        }
        if($checkarray[13] == 'red'){
            $emptytemplate->todaymath_red = 'red';
        }
        if($checkarray[14] == 'red'){
            $emptytemplate->nextmath_red = 'red';
        }
        if($checkarray[15] == 'red'){
            $emptytemplate->todayict_red = 'red';
        }
        if($checkarray[16] == 'red'){
            $emptytemplate->todayenglish_red = 'red';
        }
        if($checkarray[17] == 'red'){
            $emptytemplate->nextenglish_red = 'red';
        }
        if($checkarray[18] == 'red'){
            $emptytemplate->nextict_red = 'red';
        }
        if($checkarray[19] == 'red'){
            $emptytemplate->activity_red = 'red';
        }
        if($checkarray[20] == 'red'){
            $emptytemplate->activityksb_red = 'red';
        }
        if($checkarray[21] == 'red'){
            $emptytemplate->safeguarding_red = 'red';
        }
        if($checkarray[22] == 'red'){
            $emptytemplate->agreedaction_red = 'red';
        }
        if($checkarray[23] == 'red'){
            $emptytemplate->employercomment_red = 'red';
        }
        if($checkarray[24] == 'red'){
            $emptytemplate->apprenticecomment_red = 'red';
        }
        if($checkarray[25] == 'red'){
            $emptytemplate->progexpected_red = 'red';
        }
        if($checkarray[26] == 'red'){
            $emptytemplate->otjhexpected_red = 'red';
        }
        if($checkarray[27] == 'red'){
            $emptytemplate->alnsupport_red = 'red';
        }
        if($checkarray[28] == 'red'){
            $emptytemplate->progcom_red = 'red';
        }
        if($checkarray[29] == 'red'){
            $emptytemplate->otjhcom_red = 'red';
        }
    } elseif($type == 'editdraft'){
        $array = $lib->get_draft($userid, $courseid);
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
            $array[23] = "./classes/pdf/employercomment/$array[23]";
        }
        $emptytemplate->pdfurl = $pdfurl;
        $emptytemplate->userid = $userid;
        $emptytemplate->courseid = $courseid;
        $emptytemplate->hidden = $hidden;
        $emptytemplate->hidden1 = $hidden1;
        $emptytemplate->hidden3 = $hidden3;
        $tempValue = $array[16];
        $array[16] = $array[17];
        $array[17] = $tempValue;
        $emptytemplate->array = array_values(array($array));
    }
    echo $OUTPUT->render_from_template('local_offthejob/admin_mar', $emptytemplate);
}
unset($_SESSION['array']);
unset($_SESSION['checkarray']);
unset($_SESSION['info']);
echo $OUTPUT->footer();