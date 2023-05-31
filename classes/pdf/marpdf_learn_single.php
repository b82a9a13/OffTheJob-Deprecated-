<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
require_login();
$lib = new lib();

$courseid = $_GET['courseid'];
$id = $_GET['id'];
if(!preg_match("/^[0-9]*$/",$courseid) || 
    empty($courseid) || 
    !preg_match("/^[0-9]*$/", $id) || 
    empty($id)) {
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
$pdf = new MyPDF('P', 'mm', 'A4');

$array = $lib->get_doc_id_learn($id, $courseid);

$temp = $array[15];
$array[15] = $array[17];
$array[17] = $temp;

$learnsign = $lib->learnsign($courseid);
$ntasign = $lib->ntasign_learn($courseid);

$username = $lib->user_username();

$headersize = 32;
$font = 'Times';
$pdf->addPage('P');
$pdf->setPrintHeader(true);
$pdf->setFont($font, 'B', $headersize);
$pdf->Cell(0, 0, 'Monthly Activity Record', 0, 0, 'C', 0, '', 0);
$pdf->Ln();
$pdf->Cell(0, 0, $username, 0, 0, 'C', 0, '', 0);
$pdf->ln();

$strings = [
    'apprentice' => get_string('apprentice', 'local_offthejob'),
    'reviewd' => get_string('reviewd', 'local_offthejob'),
    'standard' => get_string('standard', 'local_offthejob'),
    'eands' => 'Employer/Store',
    'coach' => get_string('coach', 'local_offthejob'),
    'morm' => get_string('morm', 'local_offthejob'),
    'sop' => get_string('sop', 'local_offthejob'),
    'progs' => get_string('prog_stat', 'local_offthejob'),
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
    'employer_sign' => get_string('employer_sign', 'local_offthejob'),
    'nta_sign' => get_string('nta_sign', 'local_offthejob'),
];

$tablehead = 12;
$tabletext = 11;
$heighth = 10;
$heightt = 6;

//Table 1
$table1width = 190/6;
$pdf->setFont($font, 'B', $tablehead);
$pdf->setFillColor(220, 220, 220);
$pdf->Cell($table1width, $heighth, $strings['apprentice'], 1, 0, 'C', 1, '', 0);
$pdf->Cell($table1width, $heighth, $strings['reviewd'], 1, 0, 'C', 1, '', 0);
$pdf->Cell($table1width, $heighth, $strings['standard'], 1, 0, 'C', 1, '', 0);
$pdf->Cell($table1width, $heighth, $strings['eands'], 1, 0, 'C', 1, '', 0);
$pdf->Cell($table1width, $heighth, $strings['coach'], 1, 0, 'C', 1, '', 0);
$pdf->Cell($table1width, $heighth, $strings['morm'], 1, 0, 'C', 1, '', 0);
$pdf->Ln();
$pdf->setFont($font, '', $tabletext);
$length = strlen($array[2]);
$intl = 12;
if($length < strlen($array[0])){
    $length = strlen($array[0]);
    $intl = 8;
}
if ($length < strlen($array[3])){
    $length = strlen($array[3]);
    $intl = 8;
}
if ($length < strlen($array[4])){
    $length = strlen($array[4]);
    $intl = 8;
}
if ($length < strlen($array[5])){
    $length = strlen($array[5]);
    $intl = 8;
}
$table1height = (strlen($array[2]) / $intl) * $heightt;
$pdf->MultiCell($table1width, $table1height, $array[0], 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell($table1width, $table1height, $array[1], 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell($table1width, $table1height, $array[2], 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell($table1width, $table1height, $array[3], 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell($table1width, $table1height, $array[4], 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell($table1width, $table1height, $array[5], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();
$pdf->Cell(0,0,'');

//Table 2
$table2width = 190/7;
$pdf->Ln();
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell($table2width*7, $heighth, $strings['sop'], 1, 0, 'C', 1, '', 0);
$pdf->Ln();

$stringg = preg_replace('/\s+/', '-', $array[30]);
$actualheight = $heightt;
$heightt = 6 * (strlen($stringg) / 27);

$pdf->setFont($font, '', $tabletext);
$pdf->MultiCell($table2width, $heightt, '%', 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell($table2width, $heightt, $array[6], 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell($table2width, $heightt, 'Target %',1 ,'C', 1, 0, '', '', true);
$pdf->MultiCell($table2width, $heightt, $array[27], 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell($table2width, $heightt, 'Comments', 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell($table2width * 2, $heightt, $array[30], 1, 'C', 0, 0, '', '', true);
$pdf->Ln();
$heightt = $actualheight;

$stringg = preg_replace('/\s+/', '-', $array[31]);
$actualheight = $heightt;
$heightt = 6 * (strlen($stringg) / 27);
$pdf->MultiCell($table2width, $heightt, 'Hours', 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell($table2width, $heightt, $array[7], 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell($table2width, $heightt, 'Target Hours', 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell($table2width, $heightt, $array[28], 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell($table2width, $heightt, 'Comments', 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell($table2width * 2, $heightt, $array[31], 1, 'C', 0, 0, '', '', true);
$heightt = 6;
$pdf->Ln();
$pdf->Cell(0, 0, '');

//Table 3
$table3width = 190/2;
$table3height = 12.5;
$pdf->Ln();
$pdf->setFont($font, 'B', $tablehead);
$pdf->MultiCell($table3width, $table3height, $strings['recap_act'], 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell($table3width, $table3height, $strings['impactu'], 1, 'C', 1, 0, '', '', true);
$pdf->Ln();
$pdf->setFont($font, '', $tabletext);
$length = 0;
$length = strlen($array[8]);
if ($length < strlen($array[9])){
    $length = strlen($array[9]);
}
$length = $length / 36;
$t3height = $heightt * $length;
$pdf->MultiCell($table3width, $t3height, $array[8], 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell($table3width, $t3height, $array[9], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();
$pdf->Cell(0, 0, '');

//Table 4
$table4width = 190/4;
$table4height = 12.5;
$pdf->Ln();
$pdf->setFont($font, 'B', $tablehead);
$pdf->MultiCell($table4width*3, $table4height, $strings['detail_tal'], 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell($table4width, $table4height, $strings['mod_ksb'], 1, 'C', 1, 0, '', '', true);
$pdf->Ln();
$pdf->setFont($font, '', $tabletext);
$length = 0;
$length1 = strlen($array[10]);
$length1 = $length1 / 62;
$length2 = strlen($array[11]);
$length2 = $length2 / 27;
if($length1 > $length2){
    $length = $length1;
} else {
    $length = $length2;
}
$t4height = $heightt * $length;
$pdf->MultiCell($table4width*3, $t4height, $array[10], 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell($table4width, $t4height, $array[11], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();
$pdf->Cell(0, 0, '');

//Table 5
$table5width = 190;
$pdf->Ln();
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell($table5width, $heighth, $strings['impact_w'], 1, 0, 'C', 1, '', 0);
$pdf->Ln();
$pdf->setFont($font, '', $tabletext);
$length = strlen($array[12]);
$t5width = $heightt * $length;
$pdf->MultiCell($table5width, $t5height, $array[12], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();
$pdf->Cell(0, 0, '');

//Table 6
$table6width = 190/5;
$pdf->Ln();
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell(190, $heighth, $strings['fsp'], 1, 0, 'C', 1, '', 0);
$pdf->Ln();
$pdf->Cell($table6width, $heighth, '', 1, 0, 'C', 1, '', 0);
$pdf->Cell($table6width*2, $heighth, $strings['learn_t'], 1, 0, 'C', 1, '', 0);
$pdf->Cell($table6width*2, $heighth, $strings['target_next'], 1, 0, 'C', 1, '', 0);
$pdf->Ln();
$pdf->setFont($font, '', $tabletext);
//Math row
$length = 0;
$length = strlen($array[13]);
if($length < strlen($array[14])){
    $length = strlen($array[14]);
}
$length = $length / 32;
$t6height = $heightt * $length;
$pdf->MultiCell($table6width, $t6height, $strings['math'], 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell($table6width*2, $t6height, $array[13], 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell($table6width*2, $t6height, $array[14], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();
//English row
$length = 0;
$length = strlen($array[17]);
if($length < strlen($array[16])){
    $length = strlen($array[16]);
}
$length = $length / 32;
$t6height = $heightt * $length;
$pdf->MultiCell($table6width, $t6height, 'English', 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell($table6width * 2, $t6height, $array[17], 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell($table6width * 2, $t6height, $array[16], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();

//ict row
$length = 0;
$length = strlen($array[15]);
if($length < strlen($array[18])){
    $length = strlen($array[18]);
}
$length = $length / 32;
$t6height = $heightt * $length;
$pdf->MultiCell($table6width, $t6height, $strings['ict'], 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell($table6width*2, $t6height, $array[15], 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell($table6width*2, $t6height, $array[18], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();
$pdf->Cell(0,0,'');

//Table 6.5
$table65width = 190;
$pdf->Ln();
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell($table65width, $heighth, 'ALN Support delivered toady', 1, 0, 'C', 1, '', 0);
$pdf->Ln();
$pdf->setFont($font, '', $tabletext);
$pdf->MultiCell($table65width, $heightt, $array[29], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();
$pdf->Cell(0,0,'');

//Table 7
$table7width = 190/4;
$pdf->Ln();
$pdf->setFont($font, 'B', $tablehead);
$pdf->MultiCell($table7width*3, $heighth+2, $strings['act_sum'], 1, 'C', 1, 0, '', '', true);
$pdf->MultiCell($table7width, $heighth+2, $strings['mod_ksb'], 1, 'C', 1, 0, '', '', true);
$pdf->Ln();
$pdf->setFont($font, '', $tabletext);
$length = 0;
$length1 = strlen($array[19]);
$length1 = $length1 / 60;
$length2 = strlen($array[20]);
$length2 = $length2 / 25;
if($length1 > $length2){
    $length = $length1;
} else {
    $length = $length2;
}
$t7height = $heightt * $length;
$pdf->MultiCell($table7width*3, $t7height, $array[19], 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell($table7width, $t7height, $array[20], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();
$pdf->Cell(0,0,'');

//Table 8
$table8width = 190;
$pdf->Ln();
$pdf->setFont($font, 'B', $tablehead);
$pdf->MultiCell($table8width, $heighth, $strings['safeguarding'], 1, 'C', 1, 0, '', '', true);
$pdf->Ln();
$pdf->setFont($font, '', $tabletext);
$pdf->MultiCell($table8width, $heighth, $array[21], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();
$pdf->Cell(0,0,'');

//Table 9
$checkpage = $pdf->PageNo();
$table9width = 190;
$pdf->Ln();
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell($table9width, $heighth, $strings['agreed_act'], 1, 0, 'C', 1, '', 0);
$pdf->Ln();
$pdf->setFont($font, '', $tabletext);
$pdf->MultiCell($table9width, $heightt, $array[22], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();
$pdf->Cell(0,0,'');
if($pdf->PageNo() == 2 && $checkpage == 1){
    $pdf->Ln();
}

$coursename = $lib->get_coursename($courseid)->fullname;

//Table 10
$table10width = 190 / 4;
$pdf->Ln();
$length = 0;
$length = strlen($array[22]);
if($length < 160){
    $length = 160;
}
$length = $length / 80;
$t10height = $heightt * $length;
$pdf->setFont($font, 'B', $tablehead);
$pdflocation = explode('/',$array[23]);
$pdflocation = end($pdflocation);
$pdf->MultiCell($table10width, $t10height, $strings['employer_comment'], 1, 'C', 1, 0, '', '', true);
$pdf->setFont($font, '', $tabletext);
$pdf->MultiCell($table10width*3, $t10height, "MonthlyActivityRecord-$username-$coursename-EmployerComment.pdf", 1, 'L', 0, 0, '', '', true);
$pdf->Ln();
$pdf->Cell(0,0,'');

//Table 11
$table11width = 190;
$pdf->Ln();
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell($table11width, $heighth, $strings['apprentice_comment'], 1, 0, 'C', 1,'', 0);
$pdf->Ln();
$pdf->setFont($font, '', $tabletext);
$length = 0;
$length = strlen($array[23]);
$length = $length / 66;
$t11height = $heightt * $length;
$pdf->MultiCell($table11width, $t11height, $array[24], 1, 'L', 0, 0, '', '', true);
$pdf->Ln();

//Signatures
$signwidth = 190/2;
$pdf->setFont($font, '', 13);
//learner signature
$pdf->Text(10, $pdf->GetY(), $strings['learner_sign']);
if($array[26] !== null && !empty($array[26])){
    $learnsign = $lib->learnsign($courseid);
    $image = base64_decode((str_replace('data:image/jpeg;base64,', '', $learnsign)));
    $pdf->Image("@".$image, 10,$pdf->GetY()+10,$signwidth,50);
}
//nta signature
$pdf->Text(($signwidth + 10), $pdf->GetY(), $strings['nta_sign']);
if($array[25] !== null && !empty($array[25])){
    $ntasign = $lib->ntasign_learn($courseid);
    $image = base64_decode((str_replace('data:image/jpeg;base64,', '', $ntasign)));
    $pdf->Image("@".$image, ($signwidth + 10),$pdf->GetY()+10,$signwidth,50);
}

$pdf->Output("MonthlyActivityRecord-$username-$coursename.pdf");

GLOBAL $USER;
\local_offthejob\event\viewed_mar_pdf_learner::create(array('context' => $context, 'relateduserid' => $USER->id, 'courseid' => $courseid, 'other' => $id))->trigger();