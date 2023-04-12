<?php
require_once(__DIR__.'/../../../../config.php');
use local_offthejob\lib;
$lib = new lib;

if(isset($_POST['submit'])){
    $error = false;
    $total = 0;

    $userid = $_POST['userid'];
    $courseid = $_POST['courseid'];
    if(!preg_match("/^[0-9]*$/", $userid) || empty($userid)){
        header('Location: ./../../teacher.php');
        exit();
    } elseif(!preg_match("/^[0-9]*$/", $courseid) || empty($courseid)){
        header('Location: ./../../teacher.php');
        exit();
    }

    $leftcheck = [];
    $name = $_POST['name'];
    if(!preg_match("/^[a-z A-Z ' -]*$/", $name) || empty($name)){
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
    if(!preg_match("/^[a-z A-Z&]*$/", $epao) || empty($epao)){
        $error = true;
        $total++;
        $leftcheck[6] = 'red';
    }

    $fundsource = $_POST['fundsource'];
    if(($fundsource !== 'levy' && $fundsource !== 'contrib') || empty($fundsource)){
        $error = true;
        $total++;
        $leftcheck[7] = 'red';
    }

    $bskbrm = $_POST['bskbrm'];
    if(!preg_match("/^[0-9]*$/", $bskbrm) || empty($bskbrm)){
        $error = true;
        $total++;
        $leftcheck[8] = 'red';
    }

    $bskre = $_POST['bskre'];
    if(!preg_match("/^[0-9]*$/", $bskre) || empty($bskre)){
        $error = true;
        $total++;
        $leftcheck[9] = 'red';
    }

    $learns = $_POST['learns'];
    if(!preg_match("/^[a-z A-Z]*$/", $learns) || empty($learns)){
        $error = true;
        $total++;
        $leftcheck[10] = 'red';
    }

    $sslearnr = $_POST['sslearnr'];
    if(!preg_match("/^[0-9]*$/", $sslearnr) || empty($sslearnr)){
        $error = true;
        $total++;
        $leftcheck[11] = 'red';
    }

    $ssemployr = $_POST['ssemployr'];
    if(!preg_match("/^[0-9]*$/", $ssemployr) || empty($ssemployr)){
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
    if(!preg_match("/^[0-9.]*$/", $annuall) || empty($annuall)){
        $error = true;
        $total++;
        $leftcheck[15] = 'red';
    }

    $pdhours = $_POST['pdhours'];
    if(!preg_match("/^[0-9]*$/", $pdhours) || empty($pdhours)){
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
    if(!preg_match("/^[a-z A-Z0-9.,&]*$/", $recopl) || empty($recopl)){
        $error = true;
        $total++;
        $leftcheck[21] = 'red';
    }

    //Modules
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
            $modped = $_POST["modped$int"];
            if($modped !== null && !empty($modped)){
                $modped = new DateTime($modped);
                $modped = $modped->format('U');
            } else {
                $modped = null;
                $error = true;
                $total++;
                $modcheck[$int][2] = 'red';
            }
            $modw = $_POST["modw$int"];
            if(!preg_match("/^[0-9]*$/", $modw) || empty($modw)){
                $error = true;
                $total++;
                $modcheck[$int][3] = 'red';
            }
            $modotjh = $_POST["modotjh$int"];
            if(!preg_match("/^[0-9.]*$/", $modotjh) || empty($modotjh)){
                $error = true;
                $total++;
                $modcheck[$int][4] = 'red';
            }
            $modmod = $_POST["modmod$int"];
            if(!preg_match("/^[a-z A-Z0-9,]*$/", $modmod) || empty($modmod)){
                $error = true;
                $total++;
                $modcheck[$int][5] = 'red';
            }
            $modotjt = $_POST["modotjt$int"];
            if(!preg_match("/^[a-z A-Z0-9.,&]*$/", $modotjt) || empty($modotjt)){
                $error = true;
                $total++;
                $modcheck[$int][6] = 'red';
            }
            array_push($modarray, [$modname, $modpsd, $modped, $modw, $modotjh, $modmod, $modotjt, $int]);
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
            if($int !== 2){
                $fsname = $_POST["fsname$int"];
                if(!preg_match("/^[a-z A-Z]*$/", $fsname) || empty($fsname)){
                    $error = true;
                    $total++;
                    $fscheck[$int][0] = 'red';
                }
                $fslevel = $_POST["fslevel$int"];
                if(!preg_match("/^[0-9]*$/", $fslevel) || empty($fslevel)){
                    $error = true;
                    $total++;
                    $fscheck[$int][1] = 'red';
                }
                $fsmod = $_POST["fsmod$int"];
                if(!preg_match("/^[a-z A-Z0-9&]*$/", $fsmod) || empty($fsmod)){
                    $error = true;
                    $total++;
                    $fscheck[$int][2] = 'red';
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
                    $fscheck[$int][5] = 'red';
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
                    $fscheck[$int][6] = 'red';
                }
            } elseif($int == 2) {
                $fsname = $_POST["fsname$int"];
                if(!preg_match("/^[a-z A-Z]*$/", $fsname) || empty($fsname)){
                    $error = true;
                    $total++;
                    $fscheck[$int][0] = 'red';
                }
                $fslevel = $_POST["fslevel$int"];
                if(!preg_match("/^[0-9]*$/", $fslevel)){
                    $error = true;
                    $total++;
                    $fscheck[$int][1] = 'red';
                }
                $fsmod = $_POST["fsmod$int"];
                if(!preg_match("/^[a-z A-Z0-9&]*$/", $fsmod)){
                    $error = true;
                    $total++;
                    $fscheck[$int][2] = 'red';
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
                    $fscheck[$int][5] = 'red';
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
                    $fscheck[$int][6] = 'red';
                }
            }
            array_push($fsarray, [$fsname, $fslevel, $fsmod, $fssd, $fsped, $fsusd, $fsuped, $int]);
            $int++;
        }
    }

    //Progress review
    $progcheck = [];
    $progarray = [];
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
            array_push($progarray, [$prtor, $prpr, $int]);
            $int++;
        }
    }

    $addsa = $_POST["addsa"];
    if(!preg_match("/^[a-z A-Z.,0-9]*$/", $addsa)){
        $error = true;
        $total++;
        $leftcheck[22] = 'red';
    }

    if($error == false){
        $leftarray = [$name, $employer, $startdate, $plannedendd, $lengthofprog, $otjh, $epao, $fundsource, $bskbrm, $bskre, $learns, $sslearnr, $ssemployr, $apprenhpw, $weekop, $annuall, $pdhours, $areaostren, $longtgoal, $shorttgoal, $iag, $recopl, $addsa];
        $lib->create_plan($userid, $courseid, $leftarray, $progarray, $fsarray, $modarray);
        unset($_SESSION["array"]);
        unset($_SESSION["checkarray"]);
        unset($_SESSION["progarray"]);
        unset($_SESSION["progcheck"]);
        unset($_SESSION["fsarray"]);
        unset($_SESSION["fscheck"]);
        unset($_SESSION["modcheck"]);
        unset($_SESSION["modarray"]);
        header("Location: ./../../otj_plan.php?userid=$userid&courseid=$courseid&type=success");
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

        header("Location: ./../../otj_plan.php?userid=$userid&courseid=$courseid&error=truenew&total=$total");
    }
}