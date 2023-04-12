<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

//Used for admin reports page
require_once(__DIR__.'/../../config.php');

require_login();

//Settings and Content
$context = context_system::instance();
require_capability('local/offthejob:manager', $context);

use local_offthejob\lib;

$lib = new lib;

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejob/admin_reports.php'));
$otjr = get_string('otj_report', 'local_offthejob');
$PAGE->set_title($otjr);
$PAGE->set_heading($otjr);

//Outputting and getting the data for the page
echo $OUTPUT->header();

//Template for the header of the page
$template = (object)[
    'btm' => get_string('btm', 'local_offthejob'),
    'reset' => get_string('reset', 'local_offthejob'),
    'title' => $otjr,
    'tables' => get_string('tables', 'local_offthejob'),
    'charts' => get_string('charts', 'local_offthejob'),
    'prog' => get_string('prog', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/admin_reports_head', $template);


//Getting data for tables
$template = (object)[
    'show' => get_string('show', 'local_offthejob'),
    'hide' => get_string('hide', 'local_offthejob'),
    'all_apc' => get_string('all_apc', 'local_offthejob'),
    'learner_incomps' => get_string('learner_incomps', 'local_offthejob'),
    'learner_comps' => get_string('learner_comps', 'local_offthejob'),
    'learner_behind' => get_string('learner_behind', 'local_offthejob'),
    'learner_docmis' => get_string('learner_docmis', 'local_offthejob'),
    'learner_noplan' => get_string('learner_noplan', 'local_offthejob'),
    'course' => get_string('course', 'local_offthejob'),
    'tables' => get_string('tables', 'local_offthejob'),
    'learner' => get_string('learner', 'local_offthejob'),
    'hourslog' => get_string('hourslog', 'local_offthejob'),
    'course_comp' => get_string('course_comp', 'local_offthejob'),
    'tplan_use' => get_string('tplan_use', 'local_offthejob'),
    'mar_used' => get_string('mar_used', 'local_offthejob'),
    'otjh_use' => get_string('otjh_use', 'local_offthejob'),
    'reviewd' => get_string('reviewd', 'local_offthejob'),
    'learner_signed' => get_string('learner_signed', 'local_offthejob'),
    'coach_s' => get_string('coach_s', 'local_offthejob'),
    'total_el' => get_string('total_el', 'local_offthejob')
];
$coursearray = $lib->get_enrolments();
$learnerarray = $lib->admin_learners();
$array = [];
$int = 0;
foreach($coursearray as $coursearr){
    foreach($learnerarray as $learnerarr){
        if($coursearr[2] == $learnerarr[0]){
            $array[$int] = $coursearr[2];
            $int++;
        }            
    }
}
$array = array_count_values($array);
$data = [];
foreach($coursearray as $coursearra){
    if(!empty($array[$coursearra[2]])){
        array_push($data, [$coursearra[0], $array[$coursearra[2]]]);
    }
}
asort($data);
$template->coursearray = array_values($data);
//get all users with incomplete setup
$array = $lib->admin_setup_incomplete();
$template->initialarray = array_values($array);
//Get all users with complete setup
$array = $lib->admin_setup_complete();
$template->completearray = array_values($array);
//Get data for users behind target
$array = $lib->behind_target();
$template->behindarray = array_values($array);
//Activity Records Missing Signatures
$array = $lib->doc_not_signed();
$template->signedarray = array_values($array);
//Learners without a plan
$array = $lib->admin_plan_used_who();
$template->planarray = array_values($array);
echo $OUTPUT->render_from_template('local_offthejob/admin_reports_tables', $template);

echo("<br><br>");


//Get data for charts
$arrayf = $lib->behind_target();
//[complete, incomplete]
$hourlog = [0, 0];
$coursecomp = [0, 0];
foreach($arrayf as $arra){
    if($arra[2] === 'red'){
        $hourlog[0] += 1;
    } elseif($arra[2] === 'green'){
        $hourlog[1] += 1;
    }
    if($arra[4] === 'red'){
        $coursecomp[0] += 1;
    } elseif($arra[4] === 'green'){
        $coursecomp[1] += 1;
    }
}
echo('
    <div id="report_chart" style="display: none;" class="div-border-admin-report">
    <h2 class="bold">Charts For All Learners</h2>
    <button class="btn btn-primary mb-2 mr-2 p-2" onclick="hourslogs_click()" id="hourlogs_btn">Show <b>Hours Log Target</b></button>
    <button class="btn btn-primary mb-2 mr-2 p-2" onclick="coursecomp_click()" id="coursecomp_btn">Show <b>Course Completion Target</b></button>
    <button class="btn btn-primary mb-2 mr-2 p-2" onclick="setupcompletion_click()" id="setupcompletion_btn">Show <b>Setup Completion</b></button>
    <button class="btn btn-primary mb-2 mr-2 p-2" onclick="planutilization_click()" id="planutilization_btn">Show <b>Plan Utilization</b></button>
');
$targetlabels = [get_string('behind_t', 'local_offthejob'), get_string('on_t', 'local_offthejob')];

//Hours log chart
$targetnumbers = new \core\chart_series(get_string('hourslog_t', 'local_offthejob'), $hourlog);
$targetchart = new \core\chart_pie();
$targetchart->set_title(get_string('hourslog', 'local_offthejob'));
$targetchart->add_series($targetnumbers);
$targetchart->set_labels($targetlabels);
echo('<div id="hourlogs" style="display: none;" class="inner-div-admin-report">');
    echo $OUTPUT->render($targetchart);
echo('</div>');

//Course completion chart
$targetnumbers2 = new \core\chart_series(get_string('ccompt', 'local_offthejob'), $coursecomp);
$targetchart2 = new \core\chart_pie();
$targetchart2->set_title(get_string('course_comp', 'local_offthejob'));
$targetchart2->add_series($targetnumbers);
$targetchart2->set_labels($targetlabels);
echo('<div id="coursecomp" style="display: none;" class="inner-div-admin-report">');
    echo $OUTPUT->render($targetchart2);
echo('</div>');

$completionlabels = [get_string('incomp', 'local_offthejob'), get_string('complete', 'local_offthejob')];
//setup completion chart
$arrayc = $lib->admin_setup_completion();
$completionnumbers = new \core\chart_series(get_string('setup_comp', 'local_offthejob'), $arrayc);
$completionchart = new \core\chart_pie();
$completionchart->set_title(get_string('setup_comp', 'local_offthejob'));
$completionchart->add_series($completionnumbers);
$completionchart->set_labels($completionlabels);
echo('<div id="setupcompletion" style="display: none;" class="inner-div-admin-report">');
    echo $OUTPUT->render($completionchart);
echo('</div>');

//Training plan used
$arrayp = $lib->admin_plan_used();
$plannumbers = new \core\chart_series(get_string('plan_util', 'local_offthejob'), $arrayp);
$planchart = new \core\chart_pie();
$planchart->set_title(get_string('plan_util', 'local_offthejob'));
$planchart->add_series($plannumbers);
$planchart->set_labels($completionlabels);
echo('<div id="planutilization" style="display: none;" class="inner-div-admin-report">');
    echo $OUTPUT->render($planchart);
echo('</div>');

echo('</div>');
//Javascript for charts
echo("
    <script>
        function hourslogs_click(){
            let hourslog = document.getElementById('hourlogs')
            let hourslogbtn = document.getElementById('hourlogs_btn')
            if(hourslog.style.display == 'none'){
                hourslog.style.display = 'block'
                hourslogbtn.innerHTML = 'Hide <b>Hours Log Target</b>'
                hourslogbtn.className = 'btn btn-secondary mb-2 mr-2 p-2'
            } else if(hourslog.style.display == 'block'){
                hourslog.style.display = 'none'
                hourslogbtn.innerHTML = 'Show <b>Hours Log Target</b>'
                hourslogbtn.className = 'btn btn-primary mb-2 mr-2 p-2'
            }
        }
        function coursecomp_click(){
            let coursecomp = document.getElementById('coursecomp')
            let coursecompbtn = document.getElementById('coursecomp_btn')
            if(coursecomp.style.display == 'none'){
                coursecomp.style.display = 'block'
                coursecompbtn.innerHTML = 'Hide <b>Course Completion Target</b>'
                coursecompbtn.className = 'btn btn-secondary mb-2 mr-2 p-2'
            } else if(coursecomp.style.display == 'block'){
                coursecomp.style.display = 'none'
                coursecompbtn.innerHTML = 'Show <b>Course Completion Target</b>'
                coursecompbtn.className = 'btn btn-primary mb-2 mr-2 p-2'
            }
        }
        function setupcompletion_click(){
            let setupcomp = document.getElementById('setupcompletion')
            let setupcompbtn = document.getElementById('setupcompletion_btn')
            if(setupcomp.style.display == 'none'){
                setupcomp.style.display = 'block'
                setupcompbtn.innerHTML = 'Hide <b>Setup Completion</b>'
                setupcompbtn.className = 'btn btn-secondary mb-2 mr-2 p-2'
            } else if(setupcomp.style.display == 'block'){
                setupcomp.style.display = 'none'
                setupcompbtn.innerHTML = 'Show <b>Setup Completion</b>'
                setupcompbtn.className = 'btn btn-primary mb-2 mr-2 p-2'
            }
        }
        function planutilization_click(){
            let planutil = document.getElementById('planutilization')
            let planutilbtn = document.getElementById('planutilization_btn')
            if(planutil.style.display == 'none'){
                planutil.style.display = 'block'
                planutilbtn.innerHTML = 'Hide <b>Plan Utilization</b>'
                planutilbtn.className = 'btn btn-secondary mb-2 mr-2 p-2'
            } else if(planutil.style.display == 'block'){
                planutil.style.display = 'none'
                planutilbtn.innerHTML = 'Show <b>Plan Utilization</b>'
                planutilbtn.className = 'btn btn-primary mb-2 mr-2 p-2'
            }
        }
    </script>
");
echo("<br><br>");
//creating template and calling mustache file
//get data for progress clocks
$array = $lib->get_progress_all();
//only add unquie values to array
$progarray = [];
foreach($array as $arr){
    if(in_array([$arr[7], $arr[1]], $progarray)){}else{
        array_push($progarray, [$arr[7], $arr[1]]);
    }
}
$template = (object)[
    "array" => array_values($array),
    "filarray" => array_values($progarray),
    'filter' => get_string('filter', 'local_offthejob'),
    'show_all' => get_string('show_all', 'local_offthejob'),
    'comp_p' => get_string('comp_p', 'local_offthejob'),
    'choose_ac' => get_string('choose_ac', 'local_offthejob'),
    'comp_ptitle' => get_string('comp_ptitle', 'local_offthejob'),
    'hourslog' => get_string('hourslog', 'local_offthejob'),
    'modules' => get_string('modules', 'local_offthejob'),
    'prog' => get_string('prog', 'local_offthejob'),
    'expect' => get_string('expect', 'local_offthejob'),
    'hide_all' => get_string('hide_all', 'local_offthejob')
];
echo $OUTPUT->render_from_template('local_offthejob/admin_reports_progress', $template);

echo("
    <style>
    .inner-div-admin-report, .div-border-admin-report{
        border: 2px solid #95287A;
        border-radius: 5px;
        padding: .5rem;
        margin-bottom: .75rem;
    }
    .div-border-admin-report{
        background: #F5F5F5;
    }
    .inner-div-admin-report{
        background: #FCFCFC;
    }
    </style>
");

echo $OUTPUT->footer();

\local_offthejob\event\viewed_admin_reports::create(array('context' => \context_system::instance()))->trigger();