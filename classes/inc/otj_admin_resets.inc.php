<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib();

if(isset($_POST['submit'])){
    $type = $_POST['type'];
    $userid = $_POST['userid'];
    $courseid = $_POST['courseid'];
    if(!preg_match("/^[0-9]*$/", $userid)){
        header("Location: ./../../admin.php");
    } elseif(!preg_match("/^[0-9]*$/", $courseid)){
        header("Location: ./../../admin.php");
    }
    if($type == 'learn'){
        $lib->admin_del_learn_sign($userid, $courseid);
        header('Location: ./../../admin.php');
        $_SESSION['success'] = 'learn-sign';
    } elseif($type == 'nta'){
        $lib->admin_del_nta_sign($userid, $courseid);
        header('Location: ./../../admin.php');
        $_SESSION['success'] = 'nta-sign';
    } elseif($type == 'traindelete'){
        $lib->admin_del_tplan($userid, $courseid);
        header('Location: ./../../admin.php');
        $_SESSION['success'] = 'trainplandel';
    } elseif($type == 'initialdelete'){
        $lib->admin_del_initial($userid, $courseid);
        header('Location: ./../../admin.php');
        $_SESSION['success'] = 'initialdel';
    } elseif($type == 'deldoc'){
        $docid = $_POST['docid'];
        if(!preg_match("/^[0-9]*$/",$docid) || empty($docid)){
            header('Location: ../../admin.php');
        } else {
            if($lib->admin_get_docs_ecom_used($docid) === false){
                $text = $lib->get_doc_id_ecom($docid);
                unlink("./../pdf/employercomment/$text");
            }
            $lib->admin_del_doc($docid);
            $_SESSION['success'] = 'docdeletion';
            header('Location: ./../../admin.php');
        }
    } elseif($type == 'deldraft'){
        $draftid = $_POST['id'];
        if(!preg_match("/^[0-9]*$/",$draftid) || empty($draftid)){
            header('Location: ./../../admin.php');
        } else {
            if($lib->admin_get_draft_ecom_used($draftid) === false){
                $text = $lib->admin_get_draft_ecom($draftid);
                unlink("./../pdf/employercomment/$text");
            }
            $lib->admin_del_draft($draftid);
            $_SESSION['success'] = 'draftdeletion';
            header("Location: ./../../admin.php");
        }
    }
}