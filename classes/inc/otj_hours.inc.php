<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib();
if (isset($_POST['submit'])){
    $array = [];
    $string = "Location: ../../otj_hours.php?input=success";
    //Change date to unix time stamp
    $date = $_POST["date"];
    $date = new DateTime($date);
    $date = $date->format('U');
    //Get posted values
    $activity = $_POST["activity"];
    $whatlink = $_POST["whatlink"];
    $impact = $_POST['impact'];
    $duration = $_POST["duration"];
    $initial = $_POST["initial"];
    $userid = $_POST["userid"];
    $courseid = $_POST["courseid"];
    //Array of values of function
    array_push($array, [$date, $activity, $whatlink, $impact, $duration, $initial]);
    //Check for empty values
    $checkarray = [0, 0, 0, 0, 0, 0, 0, 0];
    if(!preg_match("/^[a-zA-Z 0-9.!,\-()\/?#]*$/", $activity) || empty($activity)){
        $checkarray[0] = 1;
    }
    if(!preg_match("/^[a-zA-Z., 0-9]*$/", $whatlink) || empty($whatlink)){
        $checkarray[1] = 1;
    }
    if(!preg_match("/^[a-zA-Z 0-9.!,\-()\/?#]*$/", $impact) || empty($impact)){
        $checkarray[2] = 1;
    }
    if(!preg_match("/^[0-9.]*$/", $duration) || empty($duration)){
        $checkarray[3] = 1;
    }
    if(!preg_match("/^[a-zA-Z]*$/", $initial) || empty($initial)){
        $checkarray[4] = 1;
    }
    if(!preg_match("/^[0-9]*$/", $date) || empty($date)){
        $checkarray[5] = 1;
    }
    if(empty($activity) && empty($whatlink) && empty($impact) && empty($duration) && empty($initial) && empty($date)){
        $checkarray[6] = 1;
    }
    $type = '';
    if(!in_array(1, $checkarray)){
        $lib->hours_exists($userid, $courseid);
        $lib->new_hours($userid, $courseid, $array);
        $type = 'success';
    }
    $_SESSION['hoursarray'] = $array;
    $_SESSION['hourscheck'] = $checkarray;
    header("Location: ../../otj_hours.php?input=$type&userid=$userid&courseid=$courseid");
}