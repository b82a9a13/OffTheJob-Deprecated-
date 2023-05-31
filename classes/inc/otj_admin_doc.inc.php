<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib();

if(isset($_POST['submit'])){
    $userid = $_POST['userid'];
    $courseid = $_POST['courseid'];
    $id = $_POST['docid'];
    $error = false;
    if(!preg_match("/^[0-9]*$/",$userid) || empty($userid)){
        header("Location: ../../admin.php");
    } elseif (!preg_match("/^[0-9]*$/",$courseid) || empty($courseid)){
        header("Location: ../../admin.php");
    } elseif (!preg_match("/^[0-9]*$/",$id) || empty($id)){
        if($id == null || empty($id)){
            $type = 'editdraft';
        } else {
           header("Location: ../../admin.php");
        }
    }

    $check = [];
    $num = 0;
    //Check apprentice input
    $apprentice = $_POST['apprentice'];
    if(!preg_match("/^[a-z A-Z'\-]*$/", $apprentice) || empty($apprentice)){
        $num++;
        $error = true;
        $check[0] = 'red';
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
        $num++;
        $error = true;
        $check[1] = 'red';
    }
    //Check standard input
    $standard = $_POST['standard'];
    if(!preg_match("/^[a-z A-Z0-9()]*$/", $standard) || empty($standard)){
        $num++;
        $error = true;
        $check[2] = 'red';
    }

    //Check employer and store input
    $eands = $_POST['employerandstore'];
    if(!preg_match("/^[a-z A-Z]*$/", $eands) || empty($eands)){
        $num++;
        $error = true;
        $check[3] = 'red';
    }

    //Check coach input
    $coach = $_POST['coach'];
    if(!preg_match("/^[a-z A-Z]*$/", $coach) || empty($coach)){
        $num++;
        $error = true;
        $check[4] = 'red';
    }

    //check manager or mentor input
    $mom = $_POST['managerormentor'];
    if(!preg_match("/^[a-z A-Z]*$/", $mom) ||empty($mom)){
        $num++;
        $error = true;
        $check[5] = 'red';
    }

    //check progress input
    $progress = $_POST['progress'];
    if(!preg_match("/^[0-9]*$/", $progress) || empty($progress)){
        $num++;
        $error = true;
        $check[6] = 'red';
    }

    //check hours input
    $hours = $_POST['hours'];
    if(!preg_match("/^[0-9]*$/", $hours) || empty($hours)){
        $num++;
        $error = true;
        $check[7] = 'red';
    }

        //Check recap input
    $recap = $_POST['recap'];
    if(!preg_match("/^[a-z A-Z ,.;:!\-]*$/", $recap)){
        $num++;
        $error = true;
        $check[8] = 'red';
    }

    //Check impact input
    $impact = $_POST['impact'];
    if(!preg_match("/^[a-z A-Z ,.;:!\-]*$/", $impact)){
        $num++;
        $error = true;
        $check[9] = 'red';
    }

    //Check details input
    $details = $_POST['details'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!\-]*$/", $details) || empty($details)){
        $num++;
        $error = true;
        $check[10] = 'red';
    }

    //Check detailsksb input
    $detailsksb = $_POST['detailsksb'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!\-]*$/", $detailsksb) || empty($detailsksb)){
        $num++;
        $error = true;
        $check[11] = 'red';
    }

    //Check detailimpact input
    $detailimpact = $_POST['detailimpact'];
    if(!preg_match("/^[a-z A-Z ,.;:!\-]*$/", $detailimpact) || empty($detailimpact)){
        $num++;
        $error = true;
        $check[12] = 'red';
    }

        //Check tmath input
    $tmath = $_POST['todaymath'];
    if(!preg_match("/^[a-z A-Z ,.;:!\-]*$/", $tmath) || empty($tmath)){
        $num++;
        $error = true;
        $check[13] = 'red';
    }

    //Check nmath input
    $nmath = $_POST['nextmath'];
    if(!preg_match("/^[a-z A-Z ,.;:!\-]*$/", $nmath) || empty($nmath)){
        $num++;
        $error = true;
        $check[14] = 'red';
    }

    //Check tict input
    $tict = $_POST['todayict'];
    if(!preg_match("/^[a-z A-Z ,.;:!\-]*$/", $tict)){
        $num++;
        $error = true;
        $check[15] = 'red';
    }

    //Check today english
    $teng = $_POST['todayenglish'];
    if(!preg_match("/^[a-z A-Z ,.;:!\-]*$/", $teng) || empty($teng)){
        $num++;
        $error = true;
        $check[16] = 'red';
    }

    //Check next english
    $neng = $_POST['nextenglish'];
    if(!preg_match("/^[a-z A-Z ,.;:!\-]*$/", $neng) || empty($neng)){
        $num++;
        $error = true;
        $check[17] = 'red';
    }

    //Check nict input
    $nict = $_POST['nextict'];
    if(!preg_match("/^[a-z A-Z ,.;:!\-]*$/", $nict)){
        $num++;
        $error = true;
        $check[18] = 'red';
    }

    //Check activity input
    $activity = $_POST['activity'];
    if(!preg_match("/^[a-z A-Z ,.;:!\-]*$/", $activity) || empty($activity)){
        $num++;
        $error = true;
        $check[19] = 'red';
    }

    //Check activityksb input
    $activityksb = '';
    //Check safeguard input
    $safeguard = $_POST['safeguarding'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!\-]*$/", $safeguard) || empty($safeguard)){
        $num++;
        $error = true;
        $check[21] = 'red';
    }

    //Check aaction input
    $aaction = $_POST['agreedaction'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!\-]*$/", $aaction) || empty($aaction)){
        $num++;
        $error = true;
        $check[22] = 'red';
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
                    $employcom = "./classes/pdf/employercomment/".$filenamenew;
                }
            }
        } else {
            $num++;
            $error = true;
            $check[23] = 'red';
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
                    $employcom = "./classes/pdf/employercomment/".$filenamenew;
                }
            }
        } else {
            $num++;
            $error = true;
            $check[23] = 'red';
            $employcom = "$filetmpname";
        }
    } elseif(!empty($_POST['filename'])){
        $employcom = $_POST['filename'];
    } else{
        $num++;
        $error = true;
        $check[23] = 'red';
        $employcom = '';
    }

    //Check apprentice comment input
    $apprencom = $_POST['apprenticecomment'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!\-]*$/", $apprencom) || empty($safeguard)){
        $num++;
        $error = true;
        $check[24] = 'red';
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

    //Check expected progress
    $progexpected = $_POST['progexpected'];
    if(!preg_match("/^[0-9]*$/", $progexpected) || empty($progexpected)){
        $num++;
        $error = true;
        $check[25] = 'red';
    }

    //Check expected OTJH
    $otjhexpected = $_POST['otjhexpected'];
    if(!preg_match("/^[0-9]*$/", $otjhexpected) || empty($otjhexpected)){
        $num++;
        $error = true;
        $check[26] = 'red';
    }

    //Check aln support
    $alnsupport = $_POST['alnsupport'];
    if(!preg_match("/^[a-z A-Z]*$/", $alnsupport)){
        $num++;
        $error = true;
        $check[27] = 'red';
    }

    //Check progress comment
    $progcom = $_POST['progcom'];
    if(!preg_match("/^[a-z A-Z]*$/", $progcom) || empty($progcom)){
        $num++;
        $error = true;
        $check[28] = 'red';
    }

    //Check otjh comment
    $otjhcom = $_POST['otjhcom'];
    if(!preg_match("/^[a-z A-Z0-9]*$/", $otjhcom) || empty($otjhcom)){
        $num++;
        $error = true;
        $check[29] = 'red';
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
        $safeguard,
        $aaction,
        $employcom,
        $apprencom,
        $ntasign,
        $learnsign,
        $progexpected,
        $otjhexpected,
        $alnsupport,
        $progcom,
        $otjhcom,
    ];
    //Checks if an error has occured
    if($error == true){
        //create varaibles when an error has occured
        $array[1] = date('Y-m-d', $array[1]);
        $_SESSION['array'] = $array;
        $_SESSION["checkarray"] = $check;
        $_SESSION['info'] = [$userid, $courseid, $id, 'error'];
        header("Location: ../../otj_doc_admin.php");
    } elseif ($error == false){
        if($type == 'editdraft'){
            $lib->admin_draft_to_doc($array, $userid, $courseid);
            $_SESSION['success'] = 'draftsuccess';
        } else {
            $lib->admin_update_doc($id, $array, $userid, $courseid);
            $_SESSION['success'] = 'docupate';
        }
        header("Location: ./../../admin.php");
    }
}