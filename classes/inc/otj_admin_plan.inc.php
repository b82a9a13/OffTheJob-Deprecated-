<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib;

if(isset($_POST['submit'])){
    $userid = $_POST['userid'];
    $courseid = $_POST['courseid'];
    if(!preg_match("/^[0-9]*$/", $userid)){
        header("Location: ./../../admin.php");
    } elseif(!preg_match("/^[0-9]*$/", $courseid)){
        header("Location: ./../../admin.php");
    }

    $error = false;
    $total = 0;
    $leftcheck = [];
    $name = $_POST['name'];
    if(!preg_match("/^[a-z A-Z'\-]*$/", $name) || empty($name)){
        $error = true;
        $total++;
        $leftcheck[0] = 'red';
    }

    $employer = $_POST['employer'];
    if(!preg_match("/^[a-z A-Z]*$/", $employer) || empty($employer)){
        $error = true;
        $total++;
        $leftcheck[1] = 'red';
    }

    $startdate = $_POST['startdate'];
    if($startdate !== null && !empty($startdate)){
        $startdate = new DateTime($startdate);
        $startdate = $startdate->format('U');
    } else {
        $startdate = null;
        $error = true;
        $total++;
        $leftcheck[2] = 'red';
    }

    $plannedendd = $_POST['plannedendd'];
    if($plannedendd !== null && !empty($plannedendd)){
        $plannedendd = new DateTime($plannedendd);
        $plannedendd = $plannedendd->format('U');
    } else {
        $plannedendd = null;
        $error = true;
        $total++;
        $leftcheck[3] = 'red';
    }

    $lengthofprog = $_POST['lengthofprog'];
    if(!preg_match("/^[0-9]*$/", $lengthofprog) || empty($lengthofprog)){
        $error = true;
        $total++;
        $leftcheck[4] = 'red';
    }

    $otjh = $_POST['otjh'];
    if(!preg_match("/^[0-9]*$/", $otjh) || empty($otjh)){
        $error = true;
        $total++;
        $leftcheck[5] = 'red';
    }

    $epao = $_POST['epao'];
    if(!preg_match("/^[a-z A-Z&]*$/", $epao)){
        $error = true;
        $total++;
        $leftcheck[6] = 'red';
    }

    $fundsource = $_POST['fundsource'];
    if(($fundsource !== 'levy' && $fundsource !== 'contrib')){
        $error = true;
        $total++;
        $leftcheck[7] = 'red';
    }

    $bskbrm = $_POST['bskbrm'];
    if(!preg_match("/^[0-9]*$/", $bskbrm)){
        $error = true;
        $total++;
        $leftcheck[8] = 'red';
    }

    $bskre = $_POST['bskre'];
    if(!preg_match("/^[0-9]*$/", $bskre)){
        $error = true;
        $total++;
        $leftcheck[9] = 'red';
    }

    $learns = $_POST['learns'];
    if(!preg_match("/^[a-z A-Z]*$/", $learns)){
        $error = true;
        $total++;
        $leftcheck[10] = 'red';
    }

    $sslearnr = $_POST['sslearnr'];
    if(!preg_match("/^[0-9]*$/", $sslearnr)){
        $error = true;
        $total++;
        $leftcheck[11] = 'red';
    }

    $ssemployr = $_POST['ssemployr'];
    if(!preg_match("/^[0-9]*$/", $ssemployr)){
        $error = true;
        $total++;
        $leftcheck[12] = 'red';
    }

    $apprenhpw = $_POST['apprenhpw'];
    if(!preg_match("/^[0-9]*$/", $apprenhpw) || empty($apprenhpw)){
        $error = true;
        $total++;
        $leftcheck[13] = 'red';
    }

    $weekop = $_POST['weekop'];
    if(!preg_match("/^[0-9]*$/", $weekop) || empty($weekop)){
        $error = true;
        $total++;
        $leftcheck[14] = 'red';
    }

    $annuall = $_POST['annuall'];
    if(!preg_match("/^[0-9.]*$/", $annuall)){
        $error = true;
        $total++;
        $leftcheck[15] = 'red';
    }

    $pdhours = $_POST['pdhours'];
    if(!preg_match("/^[0-9]*$/", $pdhours)){
        $error = true;
        $total++;
        $leftcheck[16] = 'red';
    }

    $areaostren = $_POST['areaostren'];
    if(!preg_match("/^[a-z A-Z0-9,.&]*$/", $areaostren)){
        $error = true;
        $total++;
        $leftcheck[17] = 'red';
    }

    $longtgoal = $_POST['longtgoal'];
    if(!preg_match("/^[a-z A-Z0-9,.&]*$/", $longtgoal)){
        $error = true;
        $total++;
        $leftcheck[18] = 'red';
    }

    $shorttgoal = $_POST['shorttgoal'];
    if(!preg_match("/^[a-z A-Z0-9,.&]*$/", $shorttgoal)){
        $error = true;
        $total++;
        $leftcheck[19] = 'red';
    }

    $iag = $_POST['iag'];
    if(!preg_match("/^[a-z A-Z0-9,.&]*$/", $iag)){
        $error = true;
        $total++;
        $leftcheck[20] = 'red';
    }

    $recopl = $_POST['recopl'];
    if(!preg_match("/^[a-z A-Z0-9.,&]*$/", $recopl)){
        $error = true;
        $total++;
        $leftcheck[21] = 'red';
    }

    $addsa = $_POST['addsa'];
    if(!preg_match("/^[a-z A-Z0-9.,&!]*$/", $addsa)){
        $error = true;
        $total++;
        $leftcheck[22] = 'red';
    }

    //Check module array values inputted by the user
    $total = 0;
    $modcheck = [];
    $modarray = [];
    $modtotal = $_POST['modtotal'];
    if(!preg_match("/^[0-9]*$/", $modtotal) || empty($modtotal)){
        $error = true;
        $total++;
    } else {
        $int = 0;
        while($int < $modtotal){
            $modname = $_POST["modname$int"];
            if(!preg_match("/^[a-z A-Z&,]*$/", $modname) || empty($modname)){
                $error = true;
                $total++;
                $modcheck[$int][0] = 'red';
            }
            $modpsd = $_POST["modpsd$int"];
            if($modpsd !== null && !empty($modpsd)){
                $modpsd = new DateTime($modpsd);
                $modpsd = $modpsd->format('U');
            } else {
                $modpsd = null;
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
                $modcheck[$int][2] = 'red';
            }
            $modped = $_POST["modped$int"];
            if($modped !== null && !empty($modped)){
                $modped = new DateTime($modped);
                $modped = $modped->format('U');
            } else {
                $modped = null;
                $error = true;
                $total++;
                $modcheck[$int][3] = 'red';
            }
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
                $modcheck[$int][4] = 'red';
            }
            $modw = $_POST["modw$int"];
            if(!preg_match("/^[0-9]*$/", $modw) || empty($modw)){
                $error = true;
                $total++;
                $modcheck[$int][5] = 'red';
            }
            $modotjh = $_POST["modotjh$int"];
            if(!preg_match("/^[0-9.]*$/", $modotjh) || empty($modotjh)){
                $error = true;
                $total++;
                $modcheck[$int][6] = 'red';
            }
            $modmod = $_POST["modmod$int"];
            if(!preg_match("/^[a-z A-Z0-9,]*$/", $modmod) || empty($modmod)){
                $error = true;
                $total++;
                $modcheck[$int][7] = 'red';
            }
            $modotjt = $_POST["modotjt$int"];
            if(!preg_match("/^[a-z A-Z0-9.,&]*$/", $modotjt) || empty($modotjt)){
                $error = true;
                $total++;
                $modcheck[$int][8] = 'red';
            }
            $modaotjh = $_POST["modaotjhc$int"];
            if(!preg_match("/^[0-9]*$/", $modaotjh)){
                $error = true;
                $total++;
                $modcheck[$int][9] = 'red';
            }
            array_push($modarray, [$modname, $modpsd, $modrsd, $modped, $modred, $modw, $modotjh, $modmod, $modotjt, $modaotjh, $int]);
            $int++;
        }
    }

    //Check progress reviews inputs
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

    //Functional Skills
    $fscheck = [];
    $fsarray = [];
    $fstotal = $_POST['fstotal'];
    if(!preg_match("/^[0-9]*$/", $fstotal) || empty($fstotal)){
        $error = true;
        $total++;
    } else {
        $int = 0;
        while($int < $fstotal){
            $fsname = $_POST["fsname$int"];
            if(!preg_match("/^[a-z A-Z]*$/", $fsname) || empty($fsname)){
                $error = true;
                $total++;
                $fscheck[$int][0] = 'red';
            }
            if($int == 2){
                $fslevel = $_POST["fslevel$int"];
                if(!preg_match("/^[0-9]*$/", $fslevel)){
                    $error = true;
                    $total++;
                    $fscheck[$int][1] = 'red';
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
                    $fscheck[$int][3] = 'red';
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
                    $fscheck[$int][4] = 'red';
                }
                $fsaed = $_POST["fsaed$int"];
                if(!empty($fsaed)){
                    $fsaed = new DateTime($fsaed);
                    $fsaed = $fsaed->format('U');
                } elseif(empty($fsaed)){
                    $fsead = null;
                }else {
                    $fsaed = null;
                    $erorr = true;
                    $total++;
                    $fscheck[$int][5] = 'red';
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
                    $fscheck[$int][6] = 'red';
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
                    $fscheck[$int][7] = 'red';
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
                    $fscheck[$int][8] = 'red';
                }
            } else {
                $fslevel = $_POST["fslevel$int"];
                if(!preg_match("/^[0-9]*$/", $fslevel) || empty($fslevel)){
                    $error = true;
                    $total++;
                    $fscheck[$int][1] = 'red';
                }
                $fssd = $_POST["fssd$int"];
                if($fssd !== null && !empty($fssd)){
                    $fssd = new DateTime($fssd);
                    $fssd = $fssd->format('U');
                } else {
                    $fssd = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][3] = 'red';
                }
                $fsped = $_POST["fsped$int"];
                if($fsped !== null && !empty($fsped)){
                    $fsped = new DateTime($fsped);
                    $fsped = $fsped->format('U');
                } else {
                    $fsped = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][4] = 'red';
                }
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
                    $fscheck[$int][5] = 'red';
                }
                $fsusd = $_POST["fsusd$int"];
                if($fsusd !== null && !empty($fsusd)){
                    $fsusd = new DateTime($fsusd);
                    $fsusd = $fsusd->format('U');
                } else {
                    $fsusd = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][6] = 'red';
                }
                $fsuped = $_POST["fsuped$int"];
                if($fsuped !== null && !empty($fsuped)){
                    $fsuped = new DateTime($fsuped);
                    $fsuped = $fsuped->format('U');
                } else {
                    $fsuped = null;
                    $error = true;
                    $total++;
                    $fscheck[$int][7] = 'red';
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
                    $fscheck[$int][8] = 'red';
                }
            }
            $fsmod = $_POST["fsmod$int"];
            if(!preg_match("/^[a-z A-Z0-9&]*$/", $fsmod) || empty($fsmod)){
                $error = true;
                $total++;
                $fscheck[$int][2] = 'red';
            }
            array_push($fsarray, [$fsname, $fslevel, $fsmod, $fssd, $fsped, $fsaed, $fsusd, $fsuped, $fsaead, $int]);
            $int++;
        }
    }
    //Changes Log
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
    if($error == false){
        $leftarray = [$name, $employer, $startdate, $plannedendd, $lengthofprog, $otjh, $epao, $fundsource, $bskbrm, $bskre, $learns, $sslearnr, $ssemployr, $apprenhpw, $weekop, $annuall, $pdhours, $areaostren, $longtgoal, $shorttgoal, $iag, $recopl, $addsa];
        $lib->admin_update_plan($userid, $courseid, $leftarray, $progarray, $fsarray, $modarray, $logsarray);
        unset($_SESSION["array"]);
        unset($_SESSION["checkarray"]);
        unset($_SESSION["progarray"]);
        unset($_SESSION["progcheck"]);
        unset($_SESSION["fsarray"]);
        unset($_SESSION["fscheck"]);
        unset($_SESSION["modcheck"]);
        unset($_SESSION["modarray"]);
        unset($_SESSION["logarray"]);
        unset($_SESSION["logcheck"]);
        $_SESSION['success'] = 'trainplan';
        header("Location: ./../../admin.php");
    } elseif($error == true){
        $leftarray = [$name, $employer, $startdate, $plannedendd, $lengthofprog, $otjh, $epao, $fundsource, $bskbrm, $bskre, $learns, $sslearnr, $ssemployr, $apprenhpw, $weekop, $annuall, $pdhours, $areaostren, $longtgoal, $shorttgoal, $iag, $recopl, $addsa];
        $_SESSION["array"] = $leftarray;
        $_SESSION["checkarray"] = $leftcheck;

        $_SESSION["progarray"] = $progarray;
        $_SESSION["progcheck"] = $progcheck;

        $_SESSION["fsarray"] = $fsarray;
        $_SESSION["fscheck"] = $fscheck;

        $_SESSION["modcheck"] = $modcheck;
        $_SESSION["modarray"] = $modarray;

        $_SESSION["logarray"] = $logsarray;
        $_SESSION["logcheck"] = $logscheck;

        header("Location: ./../../otj_plan_admin.php?userid=$userid&courseid=$courseid&error=true");
    }
}