<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
require_login();
$lib = new lib();

$courseid = $_GET['courseid'];
if(!preg_match("/^[0-9]*$/",$courseid) || empty($courseid)) {
    header("Location: ../../learner.php");
    exit();
}
$context = context_course::instance($courseid);
require_capability('local/offthejob:student', $context);

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/tcpdf/tcpdf.php');

//Create mypdf and create header and foooter
class MYPDF extends TCPDF{
    public function Header(){
        $this->Image('./../img/ntalogo.png', $this->GetPageWidth() - 32, $this->GetPageHeight() - 22, 30, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
    }
    public function Footer(){

    }
}

//Create pdf
$pdf = new MyPDF('L', 'mm', 'A4');

$user = $lib->user_username();

//Set varaibles and create new page
$headersize = 32;
$tabletext = 11;
$font = 'Times';
$pdf->AddPage('L');
$pdf->setPrintHeader(true);
$pdf->setFont($font, 'B', $headersize);
$pdf->Cell(0, 0, 'Off The Job Hours Table - '.$user, 0, 0, 'C', 0, '', 0);
$pdf->Ln();

//Set the headers for the table
$id = 5;
$width = 270/7;
$height = 17.5;
$header = [
    get_string('date', 'local_offthejob'), 
    get_string('activity', 'local_offthejob'),
    get_string('what_unit', 'local_offthejob'),
    'What impact will this have in your role?',
    get_string('duration', 'local_offthejob'),
    get_string('initial', 'local_offthejob')
];
$tablehead = 12;
$pdf->setFillColor(138, 43, 226);
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell($id, $height, 'ID', 1, 0, 'C', 1, 0, '', 0);
$tablehead = 13;
$pdf->setFont($font, 'B', $tablehead);
$int = 0;
foreach($header as $head){
    if($int == 3){
        $pdf->MultiCell($width*2, $height, $head, 1, 'C', 1, 0, '', '', true);
    } else {
        $pdf->MultiCell($width, $height, $head, 1, 'C', 1, 0, '', '', true);
    }
    $int++;
}

//Add data in for table 
$hours = $lib->user_hours_log($courseid);
$pdf->setFont($font, '', $tabletext);
$intpos = 0;
foreach($hours as $hour){
    if($intpos % 2 == 0){
        $pdf->setFillColor(255,255,255);
    } else {
        $pdf->setFillColor(220,220,220);
    }
    $intpos++;
    $length = 0;
    $int = 0;
    $pos = 0;
    while($int < 7){
        $string = preg_replace('/\s+/', '-', $hour[$int]);
        if($length < strlen($string)){
            $length = strlen($string);
            $pos = $int;
        }
        $int++;
    }
    if($pos === 3){
        $height = 6 * ($length / 34);
    } elseif($pos === 2){
        if($length == 23){
            $height = 7.5 * ($length / 17);
        } else {
            $height = 6 * ($length / 17);
        }
    } else {
        $height = 6 * ($length / 17);
    }

    $pdf->Ln();
    $pdf->Cell($id, $height, $hour[6], 1, 0, 'C', 1, 0, '', 0);
    $pdf->Cell($width, $height, $hour[0], 1, 0, 'C', 1, 0, '', 0);
    $int = 1;
    while($int < 6){
        if($int == 5){
            $pdf->MultiCell($width, $height, $hour[$int], 1, 'C', 1, 0, '', '', true);
        } elseif($int == 3){
            $pdf->MultiCell($width*2, $height, $hour[$int], 1, 'L', 1, 0, '', '', true);
        }else {
            $pdf->MultiCell($width, $height, $hour[$int], 1, 'L', 1, 0, '', '', true);
        }
        $int++;
    }
}

//Set and display info table
$height = 6;
$percent = $lib->get_percent_hours_learn($courseid);
$expected = $lib->get_percent_expect_learn($courseid);
$otj_info = $lib->otj_info_learn($courseid);
$pdf->Ln();
$pdf->Ln();
$infowidth = 270 / 100;
$pdf->setFillColor(0, 255, 0);
$pdf->Cell($infowidth * $percent, $height, '', 0, 0, '', 1);
$pdf->setFillColor(255, 165, 0);
$expect = $infowidth * ($expected - $percent);
if($expect < 0){
    $expect = 0.1;
}
$pdf->Cell($expect, $height, "", 0, 0, '', 1);
$incomplete = 100 - $percent;
$pdf->setFillColor(255, 0, 0);
$pdf->Cell($infowidth * $incomplete, $height, '', 0, 0, '', 1);
$pdf->Ln();
$pdf->Ln();

$pdf->setFillColor(0, 255, 0);
$pdf->Cell($height, $height, '', 0, 0, '', 1);
$pdf->Cell($height, $height, 'Complete: '.$percent.'%', 0, 0, '', 0);
$pdf->Ln();

$pdf->setFillColor(255, 165, 0);
$pdf->Cell($height, $height, '', 0, 0, '', 1);
$pdf->Cell($height, $height, 'Expected: '.$expected.'%', 0, 0, '', 0);
$pdf->Ln();

$pdf->setFillColor(255, 0, 0);
$pdf->Cell($height, $height, '', 0, 0, '', 1);
$pdf->Cell($height, $height, 'Incomplete: '.$incomplete.'%', 0, 0, '', 0);
$pdf->Ln();

$pdf->setFillColor(220,220,220);
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell(270, $height, 'Info Table', 0, 0, 'C', 0);
$pdf->Ln();
$pdf->setFont($font, '', $tabletext);
$leftinfo = 270 * 0.25;
$rightinfo = 270 * 0.25;
$pdf->Cell($leftinfo, $height, 'Total Number of Hours Targeted', 1, 0, 'C', 1);
$pdf->Cell($rightinfo, $height, $otj_info[0], 1, 0, '', 0);
$pdf->Cell($leftinfo, $height, 'Total Number of Hours Left', 1, 0, 'C', 1);
$pdf->Cell($rightinfo, $height, $otj_info[1], 1, 0, '', 0);
$pdf->Ln();
$pdf->Cell($leftinfo, $height, 'Off THe Job Hours Per Week', 1, 0, 'C', 1);
$pdf->Cell($rightinfo, $height, $otj_info[2], 1, 0, '', 0);
$pdf->Cell($leftinfo, $height, 'Months on Programme', 1, 0, 'C', 1);
$pdf->Cell($rightinfo, $height, $otj_info[3], 1, 0, '', 0);
$pdf->Ln();
$pdf->Cell($leftinfo, $height, 'Weeks on Programme', 1, 0, 'C', 1);
$pdf->Cell($rightinfo, $height, $otj_info[4], 1, 0, '', 0);
$pdf->Cell($leftinfo, $height, 'Annual Leave Weeks', 1, 0, 'C', 1);
$pdf->Cell($rightinfo, $height, $otj_info[5], 1, 0, '', 0);
$pdf->Ln();

//Output pdf
$username = $lib->get_current_user();
$coursename = $lib->get_coursename($courseid)->fullname;
$pdf->Output("HoursLog-$username[1]-$coursename.pdf");

\local_offthejob\event\viewed_hours_pdf_learner::create(array('context' => $context, 'relateduserid' => $username[0], 'courseid' => $courseid))->trigger();