<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib;

if(isset($_POST['submit'])){
    //Used to check userid and courseid
    $num = 0;
    $courseid = $_POST['courseid'];
    $error = false;
    if(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
        $string = 'Location: ../../teacher.php';
        $error = true;
    } else{
        $string = "Location: ../../otj_doc_learn.php?userid=$userid&courseid=$courseid&form=true";
    }

    //Check apprentice comment input
    $apprencom = $_POST['apprenticecomment'];
    if(!preg_match("/^[a-zA-Z0-9 ,.;:!-]*$/", $apprencom) || empty($apprencom)){
        $string = "$string&input$num=apprencom";
        $num++;
        $error = true;
    }

    //Check learnsign input
    $learnsign = $_POST['learnsigndate'];
    if($learnsign <> null){
        $learnsign = new DateTime($learnsign);
        $learnsign = $learnsign->format('U');
    } else {
        $learnsign = null;
    }  

    //Check id input
    $id = $_POST['id'];
    if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
        header("Location: ./learner.php");
        exit();
    }

    $array = [
        $apprencom,
        $learnsign,
        $id
    ];

    //Checks if an error has occured
    if($error == true){
        $string = $string . "&total=$num&type=error&id=$id";
    } elseif ($error == false){
        //enter into database
        $string = $string . "&type=success&id=$id";
        $lib->save_doc_learn($courseid, $array);
    }
    header($string);
}
