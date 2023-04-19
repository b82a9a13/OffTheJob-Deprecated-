<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib;

//Check initial inputs and if sumbit is set
if(isset($_POST['submit'])){
    $userid = $_POST['userid'];
    $courseid = $_POST['courseid'];
    if(!preg_match("/^[0-9]*$/", $userid) || empty($userid)){
        header("Location: ./../../teacher.php");
        exit();
    } elseif (!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
        header("Location: ./../../teacher.php");
        exit();
    }

    //Check array values inputted by the user
    $total = 0;
    $error = false;

    $leftcheck = [];
    $areaostren = $_POST['areaostren'];
    if(!preg_match("/^[a-z A-Z0-9,.&]*$/", $areaostren)){
        $error = true;
        $total++;
        $leftcheck[0] = 'red';
    }
    $longtgoal = $_POST['longtgoal'];
    if(!preg_match("/^[a-z A-Z0-9,.&]*$/", $longtgoal)){
        $error = true;
        $total++;
        $leftcheck[1] = 'red';
    }
    $shorttgoal = $_POST['shorttgoal'];
    if(!preg_match("/^[a-z A-Z0-9,.&]*$/", $shorttgoal)){
        $error = true;
        $total++;
        $leftcheck[2] = 'red';
    }
    $iag = $_POST['iag'];
    if(!preg_match("/^[a-z A-Z0-9,.&]*$/", $iag)){
        $error = true;
        $total++;
        $leftcheck[3] = 'red';
    }
    $leftarray = [$areaostren, $longtgoal, $shorttgoal, $iag];

    $modcheck = [];
    $modarray = [];
    $modtotal = $_POST['modtotal'];
    if(!preg_match("/^[0-9]*$/", $modtotal) || empty($modtotal)){
        $error = true;
        $total++;
    } else {
        $int = 0;
        while($int < $modtotal){
            $modred = $_POST["modred$int"];
            if(!empty($modred)){
                $modred = new DateTime($modred);
                $modred = $modred->format('U');
            } elseif(empty($modred)){
                $modred = null;
            }else {
                $modred = null;
                $error = true;
                $total++;
                $modcheck[$int][0] = 'red';
            }
            $modotjt = $_POST["modotjt$int"];
            if(!preg_match("/^[a-z A-Z0-9.,&]*$/", $modotjt) || empty($modotjt)){
                $error = true;
                $total++;
                $modcheck[$int][1] = 'red';
            }
            $modrsd = $_POST["modrsd$int"];
            if($modrsd !== null && !empty($modrsd)){
                $modrsd = new DateTime($modrsd);
                $modrsd = $modrsd->format('U');
            } elseif(empty($modrsd)){
                $modrsd = null;
            }else {
                $modrsd = null;
                $error = true;
                $total++;
                $modcheck[$int][3] = 'red';
            }
            array_push($modarray, [$int, $modred, $modotjt, '', $modrsd]);
            $int++;
        }
    }

    //Check functional skills inputs
    $fscheck = [];
    $fsarray = [];
    $fstotal = $_POST['fstotal'];
    if(!preg_match("/^[0-9]*$/", $fstotal) || empty($fstotal)){
        $error = true;
        $total++;
    } else {
        $int = 0;
        while($int < $fstotal){
            if($int !== 2){
                $fsaed = $_POST["fsaed$int"];
                if($fsaed !== null && !empty($fsaed)){
                    $fsaed = new DateTime($fsaed);
                    $fsaed = $fsaed->format('U');
                } elseif(empty($fsaed)){
                    $fsead = null;
                }else {
                    $fsaed = null;
                    $erorr = true;
                    $total++;
                    $fscheck[$int][0] = 'red';
                }
                $fsaead = $_POST["fsaead$int"];
                if(!empty($fsaead)){
                    $fsaead = new DateTime($fsaead);
                    $fsaead = $fsaead->format('U');
                } elseif(empty($fsaead)){
                    $fsaead = null;
                }else {
                    $fsaead = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][1] = 'red';
                }
                $fsusd = $_POST["fsusd$int"];
                if(!empty($fsusd)){
                    $fsusd = new DateTime($fsusd);
                    $fsusd = $fsusd->format('U');
                } elseif(empty($fsusd)){
                    $fsusd = null;
                }else {
                    $fsusd = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][2] = 'red';
                }
                $fsuped = $_POST["fsuped$int"];
                if(!empty($fsuped)){
                    $fsuped = new DateTime($fsuped);
                    $fsuped = $fsuped->format('U');
                } elseif(empty($fsuped)){
                    $fsuped = null;
                }else {
                    $fsuped = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][3] = 'red';
                }
                array_push($fsarray, [$int, $fsaed, $fsaead, $fsusd, $fsuped]);
            } elseif ($int == 2){
                $fsaed = $_POST["fsaed$int"];
                if($fsaed !== null && !empty($fsaed)){
                    $fsaed = new DateTime($fsaed);
                    $fsaed = $fsaed->format('U');
                } elseif(empty($fsaed)){
                    $fsead = null;
                }else {
                    $fsaed = null;
                    $erorr = true;
                    $total++;
                    $fscheck[$int][0] = 'red';
                }
                $fsaead = $_POST["fsaead$int"];
                if(!empty($fsaead)){
                    $fsaead = new DateTime($fsaead);
                    $fsaead = $fsaead->format('U');
                } elseif(empty($fsaead)){
                    $fsaead = null;
                }else {
                    $fsaead = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][1] = 'red';
                }
                $fsusd = $_POST["fsusd$int"];
                if(!empty($fsusd)){
                    $fsusd = new DateTime($fsusd);
                    $fsusd = $fsusd->format('U');
                } elseif(empty($fsusd)){
                    $fsusd = null;
                }else {
                    $fsusd = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][2] = 'red';
                }
                $fsuped = $_POST["fsuped$int"];
                if(!empty($fsuped)){
                    $fsuped = new DateTime($fsuped);
                    $fsuped = $fsuped->format('U');
                } elseif(empty($fsuped)){
                    $fsuped = null;
                }else {
                    $fsuped = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][3] = 'red';
                }
                $fslevel = $_POST["fslevel$int"];
                if(!preg_match("/^[0-9]*$/", $fslevel)){
                    $error = true;
                    $total++;
                    $fscheck[$int][4] = 'red';
                }
                $fssd = $_POST["fssd$int"];
                if(!empty($fssd)){
                    $fssd = new DateTime($fssd);
                    $fssd = $fssd->format('U');
                } elseif(empty($fssd)){
                    $fssd = null;
                }else {
                    $fssd = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][5] = 'red';
                }
                $fsped = $_POST["fsped$int"];
                if(!empty($fsped)){
                    $fsped = new DateTime($fsped);
                    $fsped = $fsped->format('U');
                } elseif(empty($fsped)){
                    $fsped = null;
                }else {
                    $fsped = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][6] = 'red';
                }
                $fsusd = $_POST["fsusd$int"];
                if(!empty($fsusd)){
                    $fsusd = new DateTime($fsusd);
                    $fsusd = $fsusd->format('U');
                } elseif(empty($fsusd)){
                    $fsusd = null;
                }else {
                    $fsusd = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][7] = 'red';
                }
                $fsuped = $_POST["fsuped$int"];
                if(!empty($fsuped)){
                    $fsuped = new DateTime($fsuped);
                    $fsuped = $fsuped->format('U');
                } elseif(empty($fsuped)){
                    $fsuped = null;
                }else {
                    $fsuped = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][8] = 'red';
                }
                array_push($fsarray, [$int, $fsaed, $fsaead, $fsusd, $fsuped, $fslevel, $fssd, $fsped]);
            }
            $int++;
        }
    }

    //Get Progress review inputs
    $progarray = [];
    $progcheck = [];
    $progtotal = $_POST['progtotal'];
    if(!preg_match("/^[0-9]*$/", $progtotal) || empty($progtotal)){
        $error = true;
        $total++;
    } else {
        $int = 0;
        while($int < $progtotal){
            $prtor = $_POST["prtor$int"];
            if(($prtor !== 'Employer' && $prtor !== 'Learner') || empty($prtor)){
                $error = true;
                $total++;
                $progcheck[$int][0] = 'red';
            }
            $prpr = $_POST["prpr$int"];
            if($prpr !== null && !empty($prpr)){
                $prpr = new DateTime($prpr);
                $prpr = $prpr->format('U');
            } else {
                $prpr = null;
                $error = true;
                $total++;
                $progcheck[$int][1] = 'red';
            }
            $prar = $_POST["prar$int"];
            if(!empty($prar)){
                $prar = new DateTime($prar);
                $prar = $prar->format('U');
            } elseif(empty($prar)){
                $prar = null;
            }else {
                $prar = null;
                $error = true;
                $total++;
                $progcheck[$int][2] = 'red';
            }
            array_push($progarray, [$prtor, $prpr, $prar, $int]);
            $int++;
        }
    }

    //Check changes log inputs
    $logsarray = [];
    $logscheck = [];
    $logtotal = $_POST['logtotal'];
    if(!preg_match("/^[0-9]*$/", $logtotal) || empty($logtotal)){
        $error = true;
        $total++;
    } else {
        $int = 0;
        while($int < $logtotal){
            $dateofc = $_POST["dateofc$int"];
            if(!empty($dateofc) && $dateofc !== null){
                $dateofc = new DateTime($dateofc);
                $dateofc = $dateofc->format('U');
            } else {
                $dateofc = null;
                $error = true;
                $total++;
                $logscheck[$int][0] = 'red';
            }
            $log = $_POST["log$int"];
            if(!preg_match("/^[a-z A-Z.,!0-9\-]*$/", $log) || empty($log)){
                $error = true;
                $total++;
                $logscheck[$int][1] = 'red';
            }
            array_push($logsarray, [$dateofc, $log, $int]);
            $int++;
        }
    }
    if($error == true){
        $_SESSION["modarray"] = $modarray;
        $_SESSION["modcheck"] = $modcheck;
        $_SESSION["fsarray"] = $fsarray;
        $_SESSION["fscheck"] = $fscheck;
        $_SESSION["progarray"] = $progarray;
        $_SESSION["progcheck"] = $progcheck;
        $_SESSION["logarray"] = $logsarray;
        $_SESSION["logcheck"] = $logscheck;
        $_SESSION["leftarray"] = $leftarray;
        $_SESSION["leftcheck"] = $leftcheck;
        header("Location: ./../../otj_plan.php?userid=$userid&courseid=$courseid&total=$total&error=true");
    } elseif($error == false){
        $lib->update_plan($userid, $courseid, $modarray, $fsarray, $progarray, $logsarray, $leftarray);
        unset($_SESSION["modarray"]);
        unset($_SESSION["modcheck"]);
        unset($_SESSION["fsarray"]);
        unset($_SESSION["fscheck"]);
        unset($_SESSION["progarray"]);
        unset($_SESSION["progcheck"]);
        unset($_SESSION["logarray"]);
        unset($_SESSION["logcheck"]);
        unset($_SESSION['leftarray']);
        unset($_SESSION['leftcheck']);
        header("Location: ./../../otj_plan.php?userid=$userid&courseid=$courseid&type=success");
    }
}