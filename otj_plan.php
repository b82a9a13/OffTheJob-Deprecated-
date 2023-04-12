<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */
// Used for the learning plan

//requirement and validation
require_once(__DIR__.'/../../config.php');
use local_offthejob\lib;
require_login();
$lib = new lib;

$enrolss = $lib->get_enrolments();
$context = context_course::instance($enrolss[0][2]);
require_capability('local/offthejob:teacher', $context);

//Validating the data from get method
$userid = $_GET['userid'];
$courseid = $_GET['courseid'];
if(!preg_match("/^[0-9]*$/", $userid) || empty($userid)){
    header('Location: ./teacher.php');
    exit();
} elseif (!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
    header("Location: ./teacher.php");
    exit();
}
//get username and coursename
$username = $lib->get_username($userid)->username;
$coursename = $lib->get_coursename($courseid)->fullname;
//Set page varaibles
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/otj_plan.php'));
$PAGE->set_title("Training Plan - $username - $coursename");
$PAGE->set_heading("Training Plan - $username - $coursename");

//Echo data to page
echo $OUTPUT->header();

$btmtemplate = (Object)[
    'btm' => get_string('btm', 'local_offthejob'),
];
echo $OUTPUT->render_from_template('local_offthejob/btm', $btmtemplate);

//Check existence of plan
$exists = $lib->plan_exists($userid, $courseid);
if($exists == false){
    $plandata = $lib->user_plan_data($userid, $courseid);
    
    $json = file_get_contents("./templates/json/".$plandata->planfilename);
    $json = json_decode($json);
    $modulearray = [];
    $num = 0;
    $modweight = 0;
    $otjhtotal = 0;
    while($num < count($json->modules)){
        $jsonpos = $json->modules[$num];
        $modweight = $modweight + $jsonpos->mw;
        $planotjh = $plandata->otjhours * ($jsonpos->mw / 100);
        $otjhtotal = $otjhtotal + $planotjh;
        array_push($modulearray, [$jsonpos->name, $jsonpos->mw, $planotjh, $jsonpos->mod, $num]);
        $num++;
    }
    $modweight = $modweight + $json->modules[$num - 1]->mw;
    $modtotal = $num;
    $fsarray = [];
    $num = 0;
    while($num < count($json->fsd)){
        $jsonpos = $json->fsd[$num];
        array_push($fsarray, [$jsonpos->fs, $jsonpos->mod, $num]);
        $num++;
    }
    $fstotal = $num;
    $planname = $json->name;

    $template = (object)[
        'modules' => array_values($modulearray),
        'fskill' => array_values($fsarray),
        'username' => $username,
        'planname' => $planname,
        'employer' => $plandata->employerorstore,
        'totalmonth' => $plandata->totalmonths,
        'otjh' => $plandata->otjhours,
        'startdate' => date('Y-m-d',$plandata->startdate),
        'hpw' => $plandata->hoursperweek,
        'wop' => round($plandata->totalmonths * 4.34),
        'annuallw' => $plandata->annuallw,
        'submit' => get_string('submit', 'local_offthejob'),
        'fstotal' => $fstotal,
        'modtotal' => $modtotal,
        'logtotal' => 0,
        'planstate' => 'New ',
        'progreview' => array_values(array(['','','', 0])),
        'logs' => array_values(array(['','', 0])),
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
        'log_state' => 'disabled',
        'otj_inc' => 'otj_plan_new.inc.php',
        'new_otj' => 'disabled',
        'userid' => $userid,
        'courseid' => $courseid,
        'funddefault' => 'selected',
        'const_read' => 'readonly',
        'remove_r' => get_string('remove_r', 'local_offthejob'),
        'revisedd' => get_string('revisedd', 'local_offthejob'),
        'remove_l' => get_string('remove_l', 'local_offthejob'),
        'progtotal' => '1',
        'progdefault' => 'selected',
        'unused' => 'hidden disabled',
        'planned_total' => $otjhtotal,
        'modweight_total' => $modweight,
        'epaodefault' => 'selected',
        'learnsdefault' => 'selected'
    ];

    //Used for when incorrect input(s) are entered
    if(isset($_GET['error'])){
        $error = $_GET['error'];
        if($error == 'truenew'){
            $progdefault = '';
            echo("<h2 class='bold text-center' style='color: red;'>Invaild inputs are highlighted in red!</h2>");
            //Check all inputs which aren't modular
            $sarray = $_SESSION['array'];
            $checkarray = $_SESSION['checkarray'];
            $int = 0;
            if(!empty($sarray[3])){
                $template->plannedendd = date('Y-m-d', $sarray[3]);
            } else{
                $template->plannedendd_red = $checkarray[3];
            }
            if(!empty($sarray[6])){
                if($sarray[6] == 'frawards'){
                    $template->frawards = 'selected';
                } elseif($sarray[6] == 'candg'){
                    $template->candg = 'selected';
                } elseif($sarray[6] == 'innovate'){
                    $template->innovate = 'selected';
                } elseif($sarray[6] == 'dsw'){
                    $template->dsw = 'selected';
                } elseif($sarray[6] == 'nocn'){
                    $template->nocn = 'selected';
                }
                $template->epao = $sarray[6];
                $template->epaodefault = '';
            }
            if (isset($checkarray[6]) && $checkarray[6] == "red") {
                $template->epao_red = $checkarray[6];
            } 
            if(!empty($sarray[7])){
                $template->fundsource = $sarray[7];
                $template->funddefault = '';
                if($sarray[7] == 'contrib'){
                    $template->contrib = 'selected';
                } elseif ($sarray[7] = 'levy'){
                    $template->levy = 'selected';
                } else {
                    $template->fundsource_red = 'red';
                }
            } else {
                $template->fundsource_red = 'red';
            }
            if(!empty($sarray[8])){
                $template->bskbrm = $sarray[8];
            }
            if(isset($checkarray[8]) && $checkarray[8] == 'red'){
                $template->bskbrm_red = 'red';
            }
            if(!empty($sarray[9])){
                $template->bskre = $sarray[9];
            } 
            if(isset($checkarray[9]) && $checkarray[9] == 'red'){
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
            }
            if(isset($checkarray[10]) && $checkarray[10] == 'red'){
                $template->learns_red = 'red';
            }
            if(!empty($sarray[11])){
                $template->sslearnr = $sarray[11];
            }
            if(isset($checkarray[11]) && $checkarray[11] == 'red'){
                $template->sslearnr_red = 'red';
            }
            if(!empty($sarray[12])){
                $template->ssemployr = $sarray[12];
            }
            if(isset($checkarray[12]) && $checkarray[12] == 'red'){
                $template->ssemployr_red = 'red';
            }
            if(!empty($sarray[16])){
                $template->pdhours = $sarray[16];
            }
            if(isset($checkarray[16]) && $checkarray[16] == 'red'){
                $template->pdhours_red = 'red';
            }
            if(!empty($sarray[17])){
                $template->areaostren = $sarray[17];
            }
            if(isset($checkarray[17]) && $checkarray[17] == 'red'){
                $template->areaostren_red = 'red';
            }
            if(!empty($sarray[18])){
                $template->longtgoal = $sarray[18];
            }
            if(isset($checkarray[18]) && $checkarray[18] == 'red'){
                $template->longtgoal_red = 'red';
            }
            if(!empty($sarray[19])){
                $template->shorttgoal = $sarray[19];
            }
            if(isset($checkarray[19]) && $checkarray[19] == 'red'){
                $template->shorttgoal_red = 'red';
            }
            if(!empty($sarray[20])){
                $template->iag = $sarray[20];
            }
            if(isset($checkarray[20]) && $checkarray[20] == 'red'){
                $template->iag_red = 'red';
            }
            if(!empty($sarray[21])){
                $template->recopl = $sarray[21];
            }
            if(isset($checkarray[21]) && $checkarray[21] == 'red'){
                $template->recopl_red = 'red';
            }
            if(!empty($sarray[22])){
                $template->addsa = $sarray[22];
            }
            if(isset($checkarray[22]) && $checkarray[22] == 'red'){
                $template->addsa_red = 'red';
            }
    
            //Check Progress reviews inputs
            $progarray = $_SESSION['progarray'];
            $progcheck = $_SESSION['progcheck'];
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
                $progtemp[$int][2] =  '';
                $progtemp[$int][3] = $int;
                if(isset($progcheck[$int][0]) && $progcheck[$int][0] == 'red'){
                    $progtemp[$int][4] = 'red';
                }
                if(isset($progcheck[$int][1]) && $progcheck[$int][1] == 'red'){
                    $progtemp[$int][5] = 'red';
                }
                $int++;
            }
            $template->progtotal = $int;
            $template->progreview = array_values($progtemp);
    
            //Check functional skills section
            $sfsarray = $_SESSION['fsarray'];
            $fscheck = $_SESSION['fscheck'];
            $fstemp = [];
            $int = 0;
            foreach($sfsarray as $fsarr){
                if(!empty($fsarr[1])){
                    $fsarray[$int][3] = $fsarr[1];
                }
                if(!empty($fsarr[3])){
                    $fsarray[$int][4] = date('Y-m-d',$fsarr[3]);
                }
                if(!empty($fsarr[4])){
                    $fsarray[$int][5] = date('Y-m-d',$fsarr[4]);
                }
                if(!empty($fsarr[5])){
                    $fsarray[$int][6] = date('Y-m-d',$fsarr[5]);
                }
                if(!empty($fsarr[6])){
                    $fsarray[$int][7] = date('Y-m-d',$fsarr[6]);
                }
                if(isset($fscheck[$int][1]) && $fscheck[$int][1] == 'red'){
                    $fsarray[$int][8] = 'red';
                }
                if(isset($fscheck[$int][3]) && $fscheck[$int][3] == 'red'){
                    $fsarray[$int][9] = 'red';
                }
                if(isset($fscheck[$int][4]) && $fscheck[$int][4] == 'red'){
                    $fsarray[$int][10] = 'red';
                }
                if(isset($fscheck[$int][5]) && $fscheck[$int][5] == 'red'){
                    $fsarray[$int][11] = 'red';
                }
                if(isset($fscheck[$int][6]) && $fscheck[$int][6] == 'red'){
                    $fsarray[$int][12] = 'red';
                }
                $int++;
            }
            $template->fskill = array_values($fsarray);
    
            //Check all modules section
            $modarray = $_SESSION['modarray'];
            $modcheck = $_SESSION['modcheck'];
            $int = 0;
            foreach($modarray as $marr){
                if(!empty($marr[1])){
                    $modulearray[$int][5] = date('Y-m-d',$marr[1]);
                }
                if(!empty($marr[2])){
                    $modulearray[$int][6] = date('Y-m-d',$marr[2]);
                }
                if(!empty($marr[6])){
                    $modulearray[$int][7] = $marr[6];
                }
                if(isset($modcheck[$int][1]) && $modcheck[$int][1] == 'red'){
                    $modulearray[$int][8] = 'red';
                }
                if(isset($modcheck[$int][2]) && $modcheck[$int][2] == 'red'){
                    $modulearray[$int][9] = 'red';
                }
                if(isset($modcheck[$int][6]) && $modcheck[$int][6] == 'red'){
                    $modulearray[$int][10] = 'red';
                }
                $int++;
            }
            $template->modules = array_values($modulearray);
        }
    }
    echo $OUTPUT->render_from_template('local_offthejob/plan', $template);
} elseif($exists == true){
    if($_GET['type'] == 'success'){
        echo("<h2 class='bold text-center' style='color: green;'>Successful Input</h2>");
    }
    $plandata = $lib->user_plan_data($userid, $courseid);
    $json = file_get_contents("./templates/json/".$plandata->planfilename);
    $json = json_decode($json);
    //Create template for mustache file
    $template = (Object)[
        'planname' => $json->name,
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
        'edit_read' => 'readonly disabled',
        'edit_readonly' => 'readonly',
        'otj_inc' => 'otj_plan_edit.inc.php',
        'planstate' => get_string('edit', 'local_offthejob'),
        'submit' => get_string('submit', 'local_offthejob'),
        'remove_r' => get_string('remove_r', 'local_offthejob'),
        'addnewl' => get_string('addnewl', 'local_offthejob'),
        'remove_l' => get_string('remove_l', 'local_offthejob'),
        'revisedd' => get_string('revisedd', 'local_offthejob'),
        'userid' => $userid,
        'courseid' => $courseid,
        'logs' => array_values(array(['','', 0]))
    ];
    $template->ararray = array_values($lib->plan_ar_dates($userid, $courseid));
    //Add single field object to template
    $sarray = $lib->plan_single($userid, $courseid);
    if(!empty($sarray[0])){
        $template->username = $sarray[0];
    }
    if(!empty($sarray[1])){
        $template->employer = $sarray[1];
    }
    if(!empty($sarray[2])){
        $template->startdate = date('Y-m-d',$sarray[2]);
    }
    if(!empty($sarray[3])){
        $template->plannedendd = date('Y-m-d', $sarray[3]);
    }
    if(!empty($sarray[4])){
        $template->totalmonth = $sarray[4];
    }
    if(!empty($sarray[5])){
        $template->otjh = $sarray[5];
    }
    if(!empty($sarray[6])){
        if($sarray[6] == 'frawards'){
            $template->frawards = 'selected';
        } elseif($sarray[6] == 'candg'){
            $template->candg = 'selected';
        } elseif($sarray[6] == 'innovate'){
            $template->innovate = 'selected';
        } elseif($sarray[6] == 'dsw'){
            $template->dsw = 'selected';
        } elseif($sarray[6] == 'nocn'){
            $template->nocn = 'selected';
        }
        $template->epao = $sarray[6];
        $template->epaodefault = '';
    } else {
        $template->epaodefault = 'selected';
    }
    if(!empty($sarray[7])){
        $template->fundsource = $sarray[7];
        $template->funddefault = '';
        if($sarray[7] == 'contrib'){
            $template->contrib = 'selected';
        } elseif ($sarray[7] == 'levy'){
            $template->levy = 'selected';
        } else {
            $template->fundsource_red = 'red';
        }
    }
    if(!empty($sarray[8])){
        $template->bskbrm = $sarray[8];
    }
    if(!empty($sarray[9])){
        $template->bskre = $sarray[9];
    } 
    if(!empty($sarray[10])){
        $template->learns = $sarray[10];
        if($sarray[10] == 'visual'){
            $template->visual = 'selected';
        } elseif($sarray[10] == 'auditory'){
            $template->auditory = 'selected';
        } elseif($sarray[10] == 'kinaesthetic'){
            $template->kinaesthetic = 'selected';
        }
    } else {
        $template->learnsdefault = 'selected';
    }
    if(!empty($sarray[11])){
        $template->sslearnr = $sarray[11];
    }
    if(!empty($sarray[12])){
        $template->ssemployr = $sarray[12];
    }
    if(!empty($sarray[13])){
        $template->hpw = $sarray[13];
    }
    if(!empty($sarray[14])){
        $template->wop = $sarray[14];
    }
    if(!empty($sarray[15])){
        $template->annuallw = $sarray[15];
    }
    if(!empty($sarray[16])){
        $template->pdhours = $sarray[16];
    }
    if(!empty($sarray[17])){
        $template->areaostren = $sarray[17];
    }
    if(!empty($sarray[18])){
        $template->longtgoal = $sarray[18];
    }
    if(!empty($sarray[19])){
        $template->shorttgoal = $sarray[19];
    }
    if(!empty($sarray[20])){
        $template->iag = $sarray[20];
    }
    if(!empty($sarray[21])){
        $template->recopl = $sarray[21];
    }
    if(!empty($sarray[22])){
        $template->addsa = $sarray[22];
    }
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
        if($int !== 2){
            $fsarray[$int][17] = 'readonly disabled';
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

    if(isset($_GET['error'])){
        $error = $_GET["error"];
        if($error == 'true'){
            echo("<h2 class='bold text-center' style='color: red;'>Invaild inputs are highlighted in red!</h2>");
            //change td background dependant on input
            $leftarray = $_SESSION['leftarray'];
            $leftcheck = $_SESSION['leftcheck'];
            if(!empty($leftarray)){
                if(!empty($leftarray[0])){
                    $template->areaostren = $leftarray[0];
                }
                if(!empty($leftarray[1])){
                    $template->longtgoal = $leftarray[1];
                }
                if(!empty($leftarray[2])){
                    $template->shorttgoal = $leftarray[2];
                }
                if(!empty($leftarray[3])){
                    $template->iag = $leftarray[3];
                }
            }
            if(!empty($leftcheck)){
                if($leftcheck[0] == 'red'){
                    $template->areaostren_red = 'red';
                }
                if($leftcheck[1] == 'red'){
                    $template->longtgoal_red = 'red';
                }
                if($leftcheck[2] == 'red'){
                    $template->shorttgoal_red = 'red';
                }
                if($leftcheck[3] == 'red'){
                    $template->iag_red = 'red';
                }
            }
            //change td background dependant on input
            $modarray = $_SESSION["modarray"];
            $modcheck = $_SESSION["modcheck"];
            $num = 0;
            foreach($modarray as $modarr){
                if(!empty($modarr[1])){
                    $modulearray[$num][14] = date('Y-m-d',$modarr[1]);
                }
                if(!empty($modarr[2])){
                    $modulearray[$num][7] = $modarr[2];
                }
                if(!empty($modarr[3])){
                    $modulearray[$num][13] = $modarr[3];
                }
                if(!empty($modarr[4])){
                    $modulearray[$num][15] = date('Y-m-d',$modarr[4]);
                }
                if($modcheck[$num][0] == 'red'){
                    $modulearray[$num][11] = 'red';
                }
                if($modcheck[$num][1] == 'red'){
                    $modulearray[$num][10] = 'red';
                }
                if($modcheck[$num][2] == 'red'){
                    $modulearray[$num][12] = 'red';
                }
                if($modcheck[$num][3] == 'red'){
                    $modulearray[$num][16] = 'red';
                }
                $num++;
            }
            $template->modtotal = $num;
            $template->modules = array_values($modulearray);
            //change td background dependant on input
            $sfsarray = $_SESSION["fsarray"];
            $fscheck = $_SESSION["fscheck"];
            $num = 0;
            foreach($sfsarray as $fsarr){
                if(!empty($fsarr[1])){
                    $fsarray[$num][16] = date('Y-m-d',$fsarr[1]);
                }
                if(!empty($fsarr[2])){
                    $fsarray[$num][15] = date('Y-m-d',$fsarr[2]);
                }
                if(!empty($fsarr[3])){
                    $fsarray[$num][6] = date('Y-m-d',$fsarr[3]);
                }
                if(!empty($fsarr[4])){
                    $fsarray[$num][7] = date('Y-m-d',$fsarr[4]);
                }
                if(!empty($fsarr[5])){
                    $fsarray[$num][3] = $fsarr[5];
                }
                if(!empty($fsarr[6])){
                    $fsarray[$num][4] = date('Y-m-d',$fsarr[6]);
                }
                if(!empty($fsarr[7])){
                    $fsarray[$num][5] = date('Y-m-d',$fsarr[7]);
                }
                if($fscheck[$num][0] == 'red'){
                    $fsarray[$num][13] = 'red';
                }
                if($fscheck[$num][1] == 'red'){
                    $fsarray[$num][14] = 'red';
                }
                if($fscheck[$num][2] == 'red'){
                    $fsarray[$num][11] = 'red';
                }
                if($fscheck[$num][3] == 'red'){
                    $fsarray[$num][12] = 'red';
                }
                if($fscheck[$num][4] == 'red'){
                    $fsarray[$num][8] = 'red';
                }
                if($fscheck[$num][5] == 'red'){
                    $fsarray[$num][9] = 'red';
                }
                if($fscheck[$num][6] == 'red'){
                    $fsarray[$num][10] = 'red';
                }
                if($fscheck[$num][7] == 'red'){
                    $fsarray[$num][11] = 'red';
                }
                if($fscheck[$num][8] == 'red'){
                    $fsarray[$num][12] = 'red';
                }
                $num++;
            }
            $template->fstotal = $num;
            $template->fskill = array_values($fsarray);
            //change td background dependant on input
            $progarray = $_SESSION["progarray"];
            $progcheck = $_SESSION["progcheck"];
            $num = 0;
            foreach($progarray as $progarr){
                if(!empty($progarr[0])){
                    if($progarr[0] == 'Learner'){
                        $progtemp[$num][8] = 'selected';
                        $progtemp[$num][0] = $progarr[0];
                    } elseif ($progarr[0] == 'Employer'){
                        $progtemp[$num][9] = 'selected';
                        $progtemp[$num][0] = $progarr[0];
                    }
                } elseif(empty($progarr[0])){
                    $progtemp[$num][7] = 'selected';
                }
                if(!empty($progarr[1])){
                    $progtemp[$num][1] = date('Y-m-d',$progarr[1]);
                }
                if(!empty($progarr[2])){
                    $progtemp[$num][2] = date('Y-m-d',$progarr[2]);
                }
                if(!empty($progarr[3]) || $progarr[3] == 0){
                    $progtemp[$num][3] = $progarr[3];
                }
                if($progcheck[$num][0] == 'red'){
                    $progtemp[$num][4] = 'red';
                }
                if($progcheck[$num][1] == 'red'){
                    $progtemp[$num][5] = 'red';
                }
                if($progcheck[$num][2] == 'red'){
                    $progtemp[$num][6] = 'red';
                }
                $num++;
            }
            $template->progtotal = $num;
            $template->progreview = array_values($progtemp);
            //change td background dependant on input
            $logarray = $_SESSION["logarray"];
            $logcheck = $_SESSION["logcheck"];
            $num = 0;
            $logs = [];
            foreach($logarray as $logarr){
                if(!empty($logarr[0])){
                    $logs[$num][0] = date('Y-m-d',$logarr[0]);
                }
                if(!empty($logarr[1])){
                    $logs[$num][1] = $logarr[1];
                }
                if(!empty($logarr[2]) || $logarr[2] == 0){
                    $logs[$num][2] = $logarr[2];
                }
                if($logcheck[$num][0] == 'red'){
                    $logs[$num][3] = 'red';
                }
                if($logcheck[$num][1] == 'red'){
                    $logs[$num][4] = 'red';
                }
                $num++;
            }
            $template->logtotal = $num;
            $template->logs = array_values($logs);
        }
    }
    //Output mustache file
    echo $OUTPUT->render_from_template('local_offthejob/plan', $template);
}

echo $OUTPUT->footer();

\local_offthejob\event\viewed_plan_page::create(array('context' => $context, 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();