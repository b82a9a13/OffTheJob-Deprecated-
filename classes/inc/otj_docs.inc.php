<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib;

if(isset($_POST['submit'])){
    //Used to check userid and courseid
    $userid = $_POST['userid'];
    $courseid = $_POST['courseid'];
    $error = false;
    if(!preg_match("/^[0-9]*$/", $userid) || empty($userid)){
        $string = 'Location: ../../teacher.php';
        $error = true;
    } elseif(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
        $string = 'Location: ../../teacher.php';
        $error = true;
    } else{
        $lib->docs_exists($userid, $courseid);
        $string = "Location: ../../otj_doc.php?userid=$userid&courseid=$courseid&form=true";
    }

    //Check apprentice input
    $apprentice = $_POST['apprentice'];
    $num = 0;
    if(!preg_match("/^[a-z A-Z '\-]*$/", $apprentice) || empty($apprentice)){
        $string = "$string&input$num=apprentice";
        $num++;
        $error = true;
    }

    //check review date input
    $reviewdate = $_POST['reviewdate'];
    if($reviewdate <> null){
        $reviewdate = new DateTime($reviewdate);
        $reviewdate = $reviewdate->format('U');
    } else {
        $reviewdate = new DateTime(date('d-m-Y'));
        $reviewdate = $reviewdate->format('U');
    }
    if(!preg_match("/^[0-9]*$/", $reviewdate) || empty($reviewdate)){
        $string = "$string&input$num=reviewdate";
        $num++;
        $error = true;
    }

    //Check standard input
    $standard = $_POST['standard'];
    if(!preg_match("/^[a-z A-Z0-9()\-]*$/", $standard) || empty($standard)){
        $string = "$string&input$num=standard";
        $num++;
        $error = true;
    }

    //Check employer and store input
    $eands = $_POST['employerandstore'];
    if(!preg_match("/^[a-z A-Z\-]*$/", $eands) || empty($eands)){
        $string = "$string&input$num=eands";
        $num++;
        $error = true;
    }

    //Check coach input
    $coach = $_POST['coach'];
    if(!preg_match("/^[a-z A-Z'\-]*$/", $coach) || empty($coach)){
        $string = "$string&input$num=coach";
        $num++;
        $error = true;
    }

    //check manager or mentor input
    $mom = $_POST['managerormentor'];
    if(!preg_match("/^[a-z A-Z'\-]*$/", $mom) ||empty($mom)){
        $string = "$string&input$num=mom";
        $num++;
        $error = true;
    }

    //check progress input
    $progress = $_POST['progress'];
    if(!preg_match("/^[0-9]*$/", $progress) || empty($progress)){
        $string = "$string&input$num=progress";
        $num++;
        $error = true;
    }

    //check hours input
    $hours = $_POST['hours'];
    if(!preg_match("/^[0-9]*$/", $hours) || empty($hours)){
        $string = "$string&input$num=hours";
        $num++;
        $error = true;
    }

    //Check recap input
    $recap = $_POST['recap'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $recap)){
        $string = "$string&input$num=recap";
        $num++;
        $error = true;
    }

    //Check impact input
    $impact = $_POST['impact'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $impact)){
        $string = "$string&input$num=impact";
        $num++;
        $error = true;
    }

    //Check details input
    $details = $_POST['details'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $details) || empty($details)){
        $string = "$string&input$num=details";
        $num++;
        $error = true;
    }

    //Check detailsksb input
    $detailsksb = $_POST['detailsksb'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $detailsksb) || empty($detailsksb)){
        $string = "$string&input$num=detailsksb";
        $num++;
        $error = true;
    }

    //Check detailimpact input
    $detailimpact = $_POST['detailimpact'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $detailimpact) || empty($detailimpact)){
        $string = "$string&input$num=detailimpact";
        $num++;
        $error = true;
    }

    //Check tmath input
    $tmath = $_POST['todaymath'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $tmath) || empty($tmath)){
        $string = "$string&input$num=tmath";
        $num++;
        $error = true;
    }

    //Check nmath input
    $nmath = $_POST['nextmath'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $nmath) || empty($nmath)){
        $string = "$string&input$num=nmath";
        $num++;
        $error = true;
    }

    //Check tict input
    $tict = $_POST['todayict'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $tict)){
        $string = "$string&input$num=tict";
        $num++;
        $error = true;
    }

    //Check nict input
    $nict = $_POST['nextict'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $nict)){
        $string = "$string&input$num=nict";
        $num++;
        $error = true;
    }

    //Check activity input
    $activity = $_POST['activity'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $activity) || empty($activity)){
        $string = "$string&input$num=activity";
        $num++;
        $error = true;
    }
    $activityksb = '';

    //Check aaction input
    $aaction = $_POST['agreedaction'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $aaction) || empty($aaction)){
        $string = "$string&input$num=aaction";
        $num++;
        $error = true;
    }

    //Check employer comment file input
    $employcom = $_FILES['file'];
    if(!empty($_FILES['file']['name']) && empty($_POST['filename'])){
        $filename = $_FILES['file']['name'];
        $filetmpname = $_FILES['file']['tmp_name'];
        $filesize = $_FILES['file']['size'];
        $fileerror = $_FILES['file']['error'];
        $filetype = $_FILES['file']['type'];
        $fileext = explode('.',$filename);
        $fileactualext = strtolower(end($fileext));
        $allowed = array('pdf');
        if(in_array($fileactualext, $allowed)){
            if($fileerror === 0){
                if($filesize < 2500000){
                    $filenamenew = "$userid-$courseid-".uniqid().".".$fileactualext;
                    $filedestination = "./../pdf/employercomment/".$filenamenew;
                    move_uploaded_file($filetmpname, $filedestination);
                    $employcom = $filenamenew;
                }
            }
        } else {
            $string = "$string&input$num=file";
            $num++;
            $error = true;
            $employcom = "$filetmpname";
        }
    } elseif(!empty($_FILES['file']['name']) && !empty($_POST['filename'])){
        $text = explode('/',$_POST['filename']);
        $text = end($text);
        unlink("./../pdf/employercomment/$text");
        $filename = $_FILES['file']['name'];
        $filetmpname = $_FILES['file']['tmp_name'];
        $filesize = $_FILES['file']['size'];
        $fileerror = $_FILES['file']['error'];
        $filetype = $_FILES['file']['type'];
        $fileext = explode('.',$filename);
        $fileactualext = strtolower(end($fileext));
        $allowed = array('pdf');
        if(in_array($fileactualext, $allowed)){
            if($fileerror === 0){
                if($filesize < 2500000){
                    $filenamenew = "$userid-$courseid-".uniqid().".".$fileactualext;
                    $filedestination = "./../pdf/employercomment/".$filenamenew;
                    move_uploaded_file($filetmpname, $filedestination);
                    $employcom = $filenamenew;
                }
            }
        } else {
            $string = "$string&input$num=file";
            $num++;
            $error = true;
            $employcom = "$filetmpname";
        }
    } elseif(!empty($_POST['filename'])){
        $employcom = $_POST['filename'];
    } else{
        $string = "$string&input$num=file";
        $num++;
        $error = true;
        $employcom = '';
    }

    //Check safeguard input
    $safeguard = $_POST['safeguarding'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $safeguard) || empty($safeguard)){
        $string = "$string&input$num=safeguard";
        $num++;
        $error = true;
    }

    //Check apprentice comment input
    $apprencom = $_POST['apprenticecomment'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $apprencom)){
        $string = "$string&input$num=apprencom";
        $num++;
        $error = true;
    }

    //Check ntasign input
    $ntasign = $_POST['ntasigndate'];
    if($ntasign <> null){
        $ntasign = new DateTime($ntasign);
        $ntasign = $ntasign->format('U');
    } else {
        $ntasign = null;
    }

    //Check learnsign input
    $learnsign = $_POST['learnsigndate'];
    if($learnsign <> null){
        $learnsign = new DateTime($learnsign);
        $learnsign = $learnsign->format('U');
    } else {
        $learnsign = null;
    }

    //Check expected OTJH
    $otjhexpected = $_POST['otjhexpected'];
    if(!preg_match("/^[0-9]*$/", $otjhexpected) || empty($otjhexpected)){
        $string = "$string&input$num=otjhexpected";
        $num++;
        $error = true;
    }

    //Check expected progress
    $progexpected = $_POST['progexpected'];
    if(!preg_match("/^[0-9]*$/", $progexpected) || empty($progexpected)){
        $string = "$string&input$num=progexpected";
        $num++;
        $error = true;
    }

    //Check today english
    $teng = $_POST['todayenglish'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $teng) || empty($teng)){
        $string = "$string&input$num=teng";
        $num++;
        $error = true;
    }

    //Check next english
    $neng = $_POST['nextenglish'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $neng) || empty($neng)){
        $string = "$string&input$num=neng";
        $num++;
        $error = true;
    }

    //Check aln support
    $alnsupport = $_POST['alnsupport'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $alnsupport)){
        $string = "$string&input$num=alnsupport";
        $num++;
        $error = true;
    }

    //Check progress comment
    $progcom = $_POST['progcom'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $progcom) || empty($progcom)){
        $string = "$string&input$num=progcom";
        $num++;
        $error = true;
    }

    //Check otjh comment
    $otjhcom = $_POST['otjhcom'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!()'\-]*$/", $otjhcom) || empty($otjhcom)){
        $string = "$string&input$num=otjhcom";
        $num++;
        $error = true;
    }

    $array = [
        $apprentice,
        $reviewdate,
        $standard,
        $eands,
        $coach,
        $mom,
        $progress,
        $hours,
        $recap,
        $impact,
        $details,
        $detailsksb,
        $detailimpact,
        $tmath,
        $nmath,
        $teng,
        $neng,
        $tict,
        $nict,
        $activity,
        $activityksb,
        $aaction,
        $employcom,
        $safeguard,
        $apprencom,
        $ntasign,
        $learnsign,
        $progexpected,
        $otjhexpected,
        $alnsupport,
        $progcom,
        $otjhcom
    ];
    //Checks if an error has occured
    if($error == true){
        $string = $string . "&total=$num&type=error";
        //check for employercomment used
        if($lib->check_employer_comment_draft($userid, $courseid) === false){
            $text = $lib->get_employer_comment_draft($userid, $courseid);
            if($employcom !== $text){
                unlink("./../pdf/employercomment/$text");
            }
        }
        //enter into temporary database
        $lib->draft_doc($userid, $courseid, $array);
    } elseif ($error == false){
        //enter into database
        $string = "Location: ../../otj_doc.php?userid=$userid&courseid=$courseid&type=success";
        $lib->save_doc($userid, $courseid, $array);
    }
    header($string);
}
