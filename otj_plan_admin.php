<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

//Used for editing training plan
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
    if(!isset($_POST['submit'])){
        header("Location: ./admin.php");
    }
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
$PAGE->set_url(new moodle_url('/local/offthejob/otj_plan_admin.php'));
$PAGE->set_title('Off The Job - Training Plan - '.$username->username.' - '.$coursename->fullname);
$PAGE->set_heading('Off The Job - Training Plan - '.$username->username.' - '.$coursename->fullname);

echo $OUTPUT->header();

$template = (object)[
    'username' => $username->username,
    'coursename' => $coursename->fullname,
    'userid' => $userid,
    'courseid' => $courseid
];
echo $OUTPUT->render_from_template('local_offthejob/admin_plan_title', $template);

if($_GET['error'] == 'true'){
    echo("<h1 class='bold text-center' style='color:red;'>Invalid inputs are highlighted in red!</h1>");
}

//Setup form
$template = (object)[
    'remove_r' => get_string('remove_r', 'local_offthejob'),
    'submit' => get_string('submit', 'local_offthejob'),
    'name' => get_string('name', 'local_offthejob'),
    'employer_txt' => get_string('employer', 'local_offthejob'),
    'startdate_txt' => get_string('startdate', 'local_offthejob'),
    'plan_ed' => get_string('plan_ed', 'local_offthejob'),
    'lop' => get_string('lop', 'local_offthejob'),
    'cap_otjh' => get_string('cap_otjh', 'local_offthejob'),
    'epao_txt' => get_string('epao', 'local_offthejob'),
    'fund_source' => get_string('fund_source', 'local_offthejob'),
    'train_p' => get_string('train_p', 'local_offthejob'),
    'initial_ass' => get_string('initial_ass', 'local_offthejob'),
    'bskbrm_txt' => get_string('bskbrm', 'local_offthejob'),
    'bskre_txt' => get_string('bskre', 'local_offthejob'),
    'sslearnr_txt' => get_string('sslearnr', 'local_offthejob'),
    'ssemployr_txt' => get_string('ssemployr', 'local_offthejob'),
    'otj_calc' => get_string('otj_calc', 'local_offthejob'),
    'apprenhpw_txt' => get_string('apprenhpw', 'local_offthejob'),
    'weekop_txt' => get_string('w_on_p', 'local_offthejob'),
    'annuall_txt' => get_string('annuall', 'local_offthejob'),
    'pdhours_txt' => get_string('pdhours', 'local_offthejob'),
    'aspire' => get_string('aspire', 'local_offthejob'),
    'areaostren_txt' => get_string('areaostren', 'local_offthejob'),
    'longtgoal_txt' => get_string('longtgoal', 'local_offthejob'),
    'shorttgoal_txt' => get_string('shorttgoal', 'local_offthejob'),
    'iag_txt' => get_string('iag_txt', 'local_offthejob'),
    'recopl_txt' => get_string('recopl', 'local_offthejob'),
    'modules_txt' => get_string('modules', 'local_offthejob'),
    'modpsd_txt' => get_string('modpsd', 'local_offthejob'),
    'modped_txt' => get_string('modped', 'local_offthejob'),
    'modred_txt' => get_string('modred', 'local_offthejob'),
    'modw_txt' => get_string('modw', 'local_offthejob'),
    'modotjh_txt' => get_string('modotjh', 'local_offthejob'),
    'modmod_txt' => get_string('modmod', 'local_offthejob'),
    'modotjt_txt' => get_string('modotjt', 'local_offthejob'),
    'modaotjhc_txt' => get_string('modaotjhc', 'local_offthejob'),
    'fsd_title' => get_string('fsd_title', 'local_offthejob'),
    'fsd_info' => get_string('fsd_info', 'local_offthejob'),
    'fs_txt' => get_string('fs', 'local_offthejob'),
    'fslevel_txt' => get_string('fslevel', 'local_offthejob'),
    'fsmod_txt' => get_string('fsmod', 'local_offthejob'),
    'fssd_txt' => get_string('startdate', 'local_offthejob'),
    'fsped_txt' => get_string('fsped', 'local_offthejob'),
    'fsaed_txt' => get_string('fsaed', 'local_offthejob'),
    'fsusd_txt' => get_string('fsusd', 'local_offthejob'),
    'fsuped_txt' => get_string('fsuped', 'local_offthejob'),
    'fsaead_txt' => get_string('fsaead', 'local_offthejob'),
    'pr_title' => get_string('pr_title', 'local_offthejob'),
    'prtor_txt' => get_string('prtor', 'local_offthejob'),
    'prpr_txt' => get_string('prpr', 'local_offthejob'),
    'prar_txt' => get_string('prar', 'local_offthejob'),
    'addsa_txt' => get_string('addsa', 'local_offthejob'),
    'addnewr' => get_string('addnewr', 'local_offthejob'),
    'change_log' => get_string('change_log', 'local_offthejob'),
    'dateofc_txt' => get_string('dateofc', 'local_offthejob'),
    'log_txt' => get_string('log', 'local_offthejob'),
    'learns_txt' => get_string('learns', 'local_offthejob'),
    'addnewl' => get_string('addnewl', 'local_offthejob'),
    'remove_l' => get_string('remove_l', 'local_offthejob'),
    'revisedd' => get_string('revisedd', 'local_offthejob'),
    'userid' => $userid,
    'courseid' => $courseid
];

//Get all single data for form
$plansingle = $lib->plan_single($userid, $courseid);
$template->username = $plansingle[0];
$template->employer = $plansingle[1];
$template->startdate = date('Y-m-d',$plansingle[2]);
$template->plannedendd = date('Y-m-d',$plansingle[3]);
$template->totalmonth = $plansingle[4];
$template->otjh = $plansingle[5];
$template->epao = $plansingle[6];

if($plansingle[6] == 'frawards'){
    $template->frawards = 'selected';
} elseif($plansingle[6] == 'candg'){
    $template->candg = 'selected';
} elseif($plansingle[6] == 'innovate'){
    $template->innovate = 'selected';
} elseif($plansingle[6] == 'dsw'){
    $template->dsw = 'selected';
} elseif($plansingle[6] == 'nocn'){
    $template->nocn = 'selected';
} else {
    $template->epaodefault = '';
}

$template->fundsource = $plansingle[7];
$template->funddefault = '';
if($plansingle[7] == 'contrib'){
    $template->contrib = 'selected';
} elseif ($plansingle[7] = 'levy'){
    $template->levy = 'selected';
} else {
    $template->fundsource_red = 'red';
}
$template->bskbrm = $plansingle[8];
$template->bskre = $plansingle[9];

$template->learns = $plansingle[10];
if($plansingle[10] == 'visual'){
    $template->visual = 'selected';
} elseif($plansingle[10] == 'auditory'){
    $template->auditory = 'selected';
} elseif($plansingle[10] == 'kinaesthetic'){
    $template->kinaesthetic = 'selected';
} else {
    $template->learnsdefault = 'selected';
}

$template->sslearnr = $plansingle[11];
$template->ssemployr = $plansingle[12];
$template->hpw = $plansingle[13];
$template->wop = $plansingle[14];
$template->annuallw = $plansingle[15];
$template->pdhours = $plansingle[16];
$template->areaostren = $plansingle[17];
$template->longtgoal = $plansingle[18];
$template->shorttgoal = $plansingle[19];
$template->iag = $plansingle[20];
$template->recopl = $plansingle[21];
$template->addsa = $plansingle[22];

//Get modules for form
$modarray = $lib->plan_modules($userid, $courseid);
$int = 0;
$modweight = 0;
$otjhtotal = 0;
$completedtotal = 0;
foreach($modarray as $marr){
    if(!empty($marr[0])){
        $modulearray[$int][0] = $marr[0];
    }
    if(!empty($marr[1])){
        $modulearray[$int][1] = $marr[1];
        $modweight = $modweight + $marr[1];
    }
    if(!empty($marr[2])){
        $modulearray[$int][2] = $marr[2];
        $otjhtotal = $otjhtotal + $marr[2];
    }
    if(!empty($marr[3])){
        $modulearray[$int][3] = $marr[3];
    }
    if(!empty($marr[4]) || $marr[4] == 0){
        $modulearray[$int][4] = $marr[4];
    }
    if(!empty($marr[5])){
        $modulearray[$int][5] = date('Y-m-d',$marr[5]);
    }
    if(!empty($marr[6])){
        $modulearray[$int][6] = date('Y-m-d',$marr[6]);
    }
    if(!empty($marr[7])){
        $modulearray[$int][7] = $marr[7];
    }
    if(!empty($marr[8])){
        $modulearray[$int][15] = date('Y-m-d',$marr[8]);
    }
    if(!empty($marr[9])){
        $modulearray[$int][14] = date('Y-m-d',$marr[9]);
    }
    if(!empty($marr[10])){
        $modulearray[$int][13] = $marr[10];
        $completedtotal = $completedtotal + $marr[10];
    }
    $int++;
}
$template->completed_total = $completedtotal;
$template->planned_total = $otjhtotal;
$template->modweight_total = $modweight;
$template->modtotal = $int;
$template->modules = array_values($modulearray);

//Get functional skills for form
$sfsarray = $lib->plan_fs($userid, $courseid);
$int = 0;
$fsarray = [];
foreach($sfsarray as $fsarr){
    if(!empty($fsarr[0])){
        $fsarray[$int][0] = $fsarr[0];
    }
    if(!empty($fsarr[1])){
        $fsarray[$int][1] = $fsarr[1];
    }
    if(!empty($fsarr[2]) || $fsarr[2] == 0){
        $fsarray[$int][2] = $fsarr[2];
    }
    if(!empty($fsarr[3])){
        $fsarray[$int][3] = $fsarr[3];
    }
    if(!empty($fsarr[4])){
        $fsarray[$int][4] = date('Y-m-d',$fsarr[4]);
    }
    if(!empty($fsarr[5])){
        $fsarray[$int][5] = date('Y-m-d',$fsarr[5]);
    }
    if(!empty($fsarr[6])){
        $fsarray[$int][6] = date('Y-m-d',$fsarr[6]);
    }
    if(!empty($fsarr[7])){
        $fsarray[$int][7] = date('Y-m-d',$fsarr[7]);
    }
    if(!empty($fsarr[8])){
        $fsarray[$int][15] = date('Y-m-d',$fsarr[8]);
    }
    if(!empty($fsarr[9])){
        $fsarray[$int][16] = date('Y-m-d',$fsarr[9]);
    }
    $int++;
}
$template->fstotal = $int;
$template->fskill = array_values($fsarray);

//Get progress reviews section
$progarray = $lib->plan_prog($userid, $courseid);
$int = 0;
$progtemp = [];
foreach($progarray as $progarr){
    if(!empty($progarr[0])){
        if($progarr[0] == 'Learner'){
            $progtemp[$int][8] = 'selected';
            $progtemp[$int][0] = $progarr[0];
        } elseif ($progarr[0] == 'Employer'){
            $progtemp[$int][9] = 'selected';
            $progtemp[$int][0] = $progarr[0];
        }
    } elseif(empty($progarr[0])){
        $progtemp[$int][7] = 'selected';
    }
    if(!empty($progarr[1])){
        $progtemp[$int][1] = date('Y-m-d',$progarr[1]);
    }
    if(!empty($progarr[2])){
        $progtemp[$int][2] = date('Y-m-d',$progarr[2]);
    }
    if(!empty($progarr[3]) || $progarr[3] == 0){
        $progtemp[$int][3] = $progarr[3];
    }
    $int++;
}
$template->progtotal = $int;
$template->progreview = array_values($progtemp);

//Get changes log
$logarray = $lib->plan_changeslog($userid, $courseid);
$larray = [];
if(empty($logarray)){
    $larray = [array('','',0)];
    $int = 1;
} elseif (!empty($logarray)){
    $int = 0;
    foreach($logarray as $logarr){
        if(!empty($logarr[0])){
            $larray[$int][0] = date('Y-m-d',$logarr[0]);
        }
        if(!empty($logarr[1])){
            $larray[$int][1] = $logarr[1];
        }
        $larray[$int][2] = $int;
        $int++;
    }
    $larray[$int][0] = null;
    $larray[$int][1] = null;
    $larray[$int][2] = $int;
    $int++;
}
$template->logtotal = $int;
$template->logs = array_values($larray);

$error = $_GET['error'];
if($error == 'true'){
    //Check and update values for modules table
    $modcheck = $_SESSION['modcheck'];
    $modarray = $_SESSION['modarray'];
    $num = 0;
    $completedtotal = 0;
    $modweight = 0;
    $otjhtotal = 0;
    foreach($modarray as $modarr){
        //Values
        $modulearray[$num][0] = $modarr[0];
        if(!empty($modarr[5])){
            $modulearray[$num][1] = $modarr[5];
            $modweight = $modweight + $modarr[5];
        }
        if(!empty($modarr[6])){
            $modulearray[$num][2] = $modarr[6];
            $otjhtotal = $otjhtotal + $modarr[6];
        }
        $modulearray[$num][3] = $modarr[7];
        $modulearray[$num][4] = $num;
        if($modarr[1] !== null){
            $modulearray[$num][5] = date('Y-m-d',$modarr[1]);
        }
        if($modarr[3] !== null){
            $modulearray[$num][6] = date('Y-m-d',$modarr[3]);
        }
        $modulearray[$num][7] = $modarr[8];
        if($modarr[4] !== null){
            $modulearray[$num][14] = date('Y-m-d',$modarr[4]);
        }
        if($modarr[2] !== null){
            $modulearray[$num][15] = date('Y-m-d',$modarr[2]);
        }
        if($modarr[9] !== null){
            $modulearray[$num][13] = $modarr[9];
            $completedtotal = $completedtotal + $modarr[9];
        }
        //background color
        if($modcheck[$num][0] == 'red'){
            $modulearray[$num][17] = 'red';
        }
        if($modcheck[$num][1] == 'red'){
            $modulearray[$num][8] = 'red';
        }
        if($modcheck[$num][2] == 'red'){
            $modulearray[$num][16] = 'red';
        }
        if($modcheck[$num][3] == 'red') {
            $modulearray[$num][9] = 'red';
        }
        if($modcheck[$num][4] == 'red'){
            $modulearray[$num][11] = 'red';
        }
        if($modcheck[$num][5] == 'red'){
            $modulearray[$num][18] = 'red';
        }
        if($modcheck[$num][6] == 'red'){
            $modulearray[$num][19] = 'red';
        }
        if($modcheck[$num][7] == 'red'){
            $modulearray[$num][20] = 'red';
        }
        if($modcheck[$num][8] == 'red'){
            $modulearray[$num][10] = 'red';
        }
        if($modcheck[$num][9] == 'red'){
            $modulearray[$num][12] = 'red';
        }
        $num++;
    }
    $template->completed_total = $completedtotal;
    $template->planned_total = $otjhtotal;
    $template->modweight_total = $modweight;
    $template->modtotal = $num;
    $template->modules = array_values($modulearray);

    //Check all data which aren't in a single table
    $sarray = $_SESSION['array'];
    $checkarray = $_SESSION['checkarray'];
    if(!empty($sarray[0])){
        $template->username = $sarray[0];
    }
    if($checkarray[0] == 'red'){
        $template->username_red = 'red';
    }
    if(!empty($sarray[1])){
        $template->employer = $sarray[1];
    }
    if($checkarray[1] == 'red'){
        $template->employer_red = 'red';
    }
    if(!empty($sarray[2])){
        $template->startdate = date('Y-m-d',$sarray[2]);
    }
    if($checkarray[2] == 'red'){
        $template->startdate_red = 'red';
    }
    if(!empty($sarray[3])){
        $template->plannedendd = date('Y-m-d',$sarray[3]);
    }
    if($checkarray[3] == 'red'){
        $template->plannedendd_red = 'red';
    }
    if(!empty($sarray[4])){
        $template->totalmonth = $sarray[4];
    }
    if($checkarray[4] == 'red'){
        $template->totalmonth_red = 'red';
    }
    if(!empty($sarray[5])){
        $template->otjh = $sarray[5];
    }
    if($checkarray[5] == 'red'){
        $template->otjh_red = 'red';
    }
    if(!empty($sarray[6])){
        if($sarray[6] == 'frawards'){
            $template->frawards = 'selected';
        } elseif ($sarray[6] == 'candg'){
            $template->candg = 'selected';
        } elseif ($sarray[6] == 'innovate'){
            $template->innovate = 'selected';
        } elseif ($sarray[6] == 'dsw'){
            $template->dsw = 'selected';
        } elseif ($sarray[6] == 'nocn'){
            $template->nocn = 'selected';
        }
        $template->epaodefault = '';
        $template->epao = $sarray[6];
    } else {
        $template->epaodefault = 'selected';
    }
    if($checkarray[6] == 'red'){
        $template->epao_red = 'red';
    }
    if(!empty($sarray[7])){
        $template->fundsource = $sarray[7];
        $template->funddefault = '';
        if($sarray[7] == 'contrib'){
            $template->contrib = 'selected';
        } elseif ($sarray[7] = 'levy'){
            $template->levy = 'selected';
        }
        $template->fundsource = $sarray[7];
    }
    if($checkarray[7] == 'red'){
        $template->fundsource_red = 'red';
    }
    if(!empty($sarray[8])){
        $template->bskbrm = $sarray[8];
    }
    if($checkarray[8] == 'red'){
        $template->bskbrm_red = 'red';
    }
    if(!empty($sarray[9])){
        $template->bskre = $sarray[9];
    }
    if($checkarray[9] == 'red'){
        $template->bskre_red = 'red';
    }
    if(!empty($sarray[10])){
        if($sarray[10] == 'visual'){
            $template->visual = 'selected';
        } elseif($sarray[10] == 'auditory'){
            $template->auditory = 'selected';
        } elseif($sarray[10] == 'kinaesthetic'){
            $template->kinaesthetic = 'selected';
        }
        $template->learnsdefault = '';
        $template->learns = $sarray[10];
    } else {
        $template->learnsdefault = 'selected';
    }
    if($checkarray[10] == 'red'){
        $template->learns_red = 'red';
    }
    if(!empty($sarray[11])){
        $template->sslearnr = $sarray[11];
    }
    if($checkarray[11] == 'red'){
        $template->sslearnr_red = 'red';
    }
    if(!empty($sarray[12])){
        $template->ssemployr = $sarray[12];
    }
    if($checkarray[12] == 'red'){
        $template->ssemployr_red = 'red';
    }
    if(!empty($sarray[13])){
        $template->hpw = $sarray[13];
    }
    if($checkarray[13] == 'red'){
        $template->hpw_red = 'red';
    }
    if(!empty($sarray[14])){
        $template->wop = $sarray[14];
    }
    if($checkarray[14] == 'red'){
        $template->wop_red = 'red';
    }
    if(!empty($sarray[15])){
        $template->annuallw = $sarray[15];
    }
    if($checkarray[15] == 'red'){
        $template->annuallw_red = 'red';
    }
    if(!empty($sarray[16])){
        $template->pdhours = $sarray[16];
    }
    if($checkarray[16] == 'red'){
        $template->pdhours_red = 'red';
    }
    if(!empty($sarray[17])){
        $template->areaostren = $sarray[17];
    }
    if($checkarray[17] == 'red'){
        $template->areaostren_red = 'red';
    }
    if(!empty($sarray[18])){
        $template->longtgoal = $sarray[18];
    }
    if($checkarray[18] == 'red'){
        $template->longtgoal_red = 'red';
    }
    if(!empty($sarray[19])){
        $template->shorttgoal = $sarray[19];
    }
    if($checkarray[19] == 'red'){
        $template->shorttgoal_red = 'red';
    }
    if(!empty($sarray[20])){
        $template->iag = $sarray[20];
    }
    if($checkarray[20] == 'red'){
        $template->iag_red = 'red';
    }
    if(!empty($sarray[21])){
        $template->recopl = $sarray[21];
    }
    if($checkarray[21] == 'red'){
        $template->recopl_red = 'red';
    }
    if(!empty($sarray[22])){
        $template->addsa = $sarray[22];
    }
    if($checkarray == 'red'){
        $template->addsa_red = 'red';
    }

    //Check and update progrss review values
    $progarray = $_SESSION['progarray'];
    $progcheck = $_SESSION['progcheck'];
    $progtemp = [];
    $int = 0;
    foreach($progarray as $progarr){
        if(!empty($progarr[0])){
            if($progarr[0] == 'Learner'){
                $progtemp[$int][8] = 'selected';
                $progtemp[$int][0] = $progarr[0];
            } elseif ($progarr[0] == 'Employer'){
                $progtemp[$int][9] = 'selected';
                $progtemp[$int][0] = $progarr[0];
            }
        } elseif(empty($progarr[0])){
            $progtemp[$int][7] = 'selected';
        }
        if($progcheck[$int][0] == 'red'){
            $progtemp[$int][4] = 'red';
        }
        if(!empty($progarr[1])){
            $progtemp[$int][1] = date('Y-m-d',$progarr[1]);
        }
        if($progcheck[$int][1] == 'red'){
            $progtemp[$int][5] = 'red';
        }
        if(!empty($progarr[2])){
            $progtemp[$int][2] = date('Y-m-d',$progarr[2]);
        }
        if($progcheck[$int][2] == 'red'){
            $progtemp[$int][6] = 'red';
        }
        if(!empty($progarr[3]) || $progarr[3] == 0){
            $progtemp[$int][3] = $progarr[3];
        }
        $int++;
    }
    $template->progtotal = $int;
    $template->progreview = array_values($progtemp);

    //Check and update functional skill values
    $sfsarray = $_SESSION['fsarray'];
    $fscheck = $_SESSION['fscheck'];
    $int = 0;
    foreach($sfsarray as $fsarr){
        if(!empty($fsarr[0])){
            $fsarray[$int][0] = $fsarr[0];
        }
        if($fscheck[$int][0] == 'red'){
            $fsarray[$int][17] = 'red';
        }
        if(!empty($fsarr[1])){
            $fsarray[$int][3] = $fsarr[1];
        }
        if($fscheck[$int][1] == 'red'){
            $fsarray[$int][8] = 'red';
        }
        if(!empty($fsarr[2])){
            $fsarray[$int][1] = $fsarr[2];
        }
        if($fscheck[$int][2] == 'red'){
            $fsarray[$int][18] = 'red';
        }
        if(!empty($fsarr[3])){
            $fsarray[$int][4] = date('Y-m-d',$fsarr[3]);
        }
        if($fscheck[$int][3] == 'red'){
            $fsarray[$int][9] = 'red';
        }
        if(!empty($fsarr[4])){
            $fsarray[$int][5] = date('Y-m-d',$fsarr[4]);
        }
        if($checkarray[$int][4] == 'red'){
            $fsarray[$int][10] = 'red';
        }
        if(!empty($fsarr[5])){
            $fsarray[$int][16] = date('Y-m-d',$fsarr[5]);
        }
        if($checkarray[$int][5] == 'red'){
            $fsarray[$int][13] = 'red';
        }
        if(!empty($fsarr[6])){
            $fsarray[$int][6] = date('Y-m-d',$fsarr[6]);
        }
        if($checkarray[$int][6] == 'red'){
            $fsarray[$int][11] = 'red';
        }
        if(!empty($fsarr[7])){
            $fsarray[$int][7] = date('Y-m-d',$fsarr[7]);
        }
        if($checkarray[$int][7]){
            $fsarray[$int][12] = 'red';
        }
        if(!empty($fsarr[8])){
            $fsarray[$int][15] = date('Y-m-d',$fsarr[8]);
        }
        if($checkarray[$int][8]){
            $fsarray[$int][14] = 'red';
        }
        $int++;
    }
    $template->fstotal = $int;
    $template->fskill = array_values($fsarray);

    //Check and update changes log values
    $logarray = $_SESSION['logarray'];
    $logcheck = $_SESSION['logcheck'];
    $logs = [];
    $num = 0;
    foreach($logarray as $logarr){
        if(!empty($logarr[0])){
            $logs[$num][0] = date('Y-m-d', $logarr[0]);
        }
        if($logcheck[$num][0] == 'red'){
            $logs[$num][3] = 'red';
        }
        if(!empty($logarr[1])){
            $logs[$num][1] = $logarr[1];
        }
        if($logcheck[$num][1] == 'red'){
            $logs[$num][4] = 'red';
        }
        if(!empty($logarr[2]) || $logarr[2] == 0){
            $logs[$num][2] = $logarr[2];
        }
        $num++;
    }
    $template->logtotal = $num;
    $template->logs = array_values($logs);
}

echo $OUTPUT->render_from_template('local_offthejob/admin_plan', $template);

echo $OUTPUT->footer();