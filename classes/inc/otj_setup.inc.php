<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib;

if(isset($_POST['submit'])){
    $totalmonths = $_POST['totalmonths'];
    $otjhours = $_POST['otjhours'];
    $eors = $_POST['eors'];
    $coach = $_POST['coach'];
    $mom = $_POST['mom'];
    $userid = $_POST['userid'];
    $courseid = $_POST['courseid'];
    $startdate = $_POST['startdate'];
    $startdate = new DateTime($startdate);
    $startdate = $startdate->format('U');
    $error = false;
    $hpw = $_POST['hpw'];
    $alw = $_POST['alw'];
    $glh = $_POST['glh'];
    $planfilename = $_POST['planfilename'];

    if(!preg_match("/^[0-9]*$/", $userid) || empty($userid)){
        header("Location: ../../teacher.php");
    } elseif (!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
        header("Location: ../../teacher.php");
    }

    $header = "Location: ../../otj_setup.php?userid=$userid&courseid=$courseid";
    $total = 0;
    if(!preg_match("/^[0-9]*$/", $totalmonths) || empty($totalmonths)){
        $header = "$header&input$total=totalmonths&text$total=$totalmonths";
        $error = true;
        $total++;
    }
    if(!preg_match("/^[0-9]*$/", $otjhours) || empty($otjhours)){
        $header = "$header&input$total=otjhours&text$total=$otjhours";
        $error = true;
        $total++;
    }
    if(!preg_match("/^[a-z A-Z]*$/", $eors) || empty($eors)){
        $header = "$header&input$total=eors&text$total=$eors";
        $error = true;
        $total++;
    }
    if(!preg_match("/^[a-z A-Z]*$/", $coach) || empty($coach)){
        $header = "$header&input$total=coach&text$total=$coach";
        $error = true;
        $total++;
    }
    if(!preg_match("/^[a-z A-Z]*$/", $mom) || empty($mom)){
        $header = "$header&input$total=mom&text$total=$mom";
        $error = true;
        $total++;
    }
    //check image base 64
    $url = $_POST['signature'];
    if(!preg_match("/^[a-zA-Z0-9+:;\/,=]*$/", $url) || empty($url)){
        $header = "$header&input$total=url";
        $error = true;
        $total++;
    }

    if(!preg_match("/^[0-9]*$/", $startdate) || empty($startdate)){
        $header = "$header&input$total=startdate";
        $error = true;
        $total++;
    }

    if(!preg_match("/^[0-9]*$/", $hpw) || empty($hpw)){
        $header = "$header&input$total=hpw";
        $error = true;
        $total++;
    }

    if(!preg_match("/^[0-9.]*$/", $alw) || empty($alw)){
        $header = "$header&input$total=alw";
        $error = true;
        $total++;
    }

    $filenames = $lib->training_plans();
    $fileerror = true;
    if(!preg_match("/^[0-9a-zA-Z.-]*$/", $planfilename) || empty($planfilename)){
        $header = "$header&input$total=planfilename";
        $error = true;
        $total++;
    } else {
        foreach($filenames as $filename){
            if($filename[1] == $planfilename){
                $fileerror = false;
            }
        }
        if($fileerror == true){
            $header = "$header&input$total=planfilename";
            $error = true;
            $total++;
        }
    }


    if($error == false){
        $header = "Location: ../../teacher.php?setup=success";
        $array = [$totalmonths, $otjhours, $eors, $coach, $mom, $url, $startdate, $hpw, $alw, $planfilename];
        $lib->setup($userid, $courseid, $array);
    } elseif($error == true){
        $header = "$header&text0=$totalmonths&text1=$otjhours&text2=$eors&text3=$coach&text4=$mom&date=$startdate&hpw=$hpw&alw=$alw";
    }

    $header = "$header&total=$total";
    header($header);

}