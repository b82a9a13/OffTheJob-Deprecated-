<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */
namespace local_offthejob;
use stdClass;

class lib{
    //Get the current userid and username
    public function get_current_user(): array{
        global $USER;
        $array = [$USER->id, ("$USER->firstname "."$USER->lastname")];
        return $array;
    }

    //Get enrolled courses for user
    public function get_enrolments(): array{
        global $USER;
        $user = [$USER->id, ("$USER->firstname "."$USER->lastname")];
        global $DB;
        $apprenticeship = 'Apprenticeships';

        $userenrolments = $DB->get_records_sql('SELECT enrolid, status FROM {user_enrolments} WHERE userid = ? AND status = ?', [$user[0], 0]); 
        $enroltable = $DB->get_records('enrol');
        $courseids = [];
        //(coursid) 
        //Filter out susspended users
        foreach($userenrolments as $userenrol){
            foreach($enroltable as $enrolt){
                if($enrolt->id == $userenrol->enrolid && $userenrol->status !== 1){
                    array_push($courseids, [$enrolt->courseid]);
                }
            }
        }
        //Only include courses which they are a teacher on
        $coursetemp = [];
        $contexts = $DB->get_records('context');
        $roleassignments = $DB->get_records_sql('SELECT * FROM {role_assignments} WHERE userid = ?', [$user[0]]);
        foreach($contexts as $context){
            foreach($roleassignments as $roleassign){
                if($roleassign->contextid == $context->id && ($roleassign->roleid == 3 || $roleassign->roleid == 4)){
                    array_push($coursetemp, [$context->instanceid]);
                }
            }
        }

        $coursefinal = []; 
        foreach($coursetemp as $ctemp){
            foreach($courseids as $cfinal){
                if($ctemp == $cfinal){
                    array_push($coursefinal, $ctemp);
                }
            }
        }
        $courseids = $coursefinal;
        
        //(course full name, course category, course id)
        $coursenames = [];
        $courses = $DB->get_records('course');
        foreach($courseids as $cours){
            foreach($courses as $cou){
                if($cours[0] == $cou->id){
                    array_push($coursenames, [$cou->fullname, $cou->category, $cou->id]);
                }
            }
        }
        
        //(course full name, apprenticeship, course id)
        $apprenticeid = $DB->get_record_sql('SELECT id FROM {course_categories} WHERE name = ?', [$apprenticeship]);
        $apprenticearray = [];
        foreach($coursenames as $coursename){
            if($coursename[1] == $apprenticeid->id){
                array_push($apprenticearray, [$coursename[0], $apprenticeship, $coursename[2]]);
            }
        }
        asort($apprenticearray);
        return $apprenticearray;
    }

    public function teacher_course_users($int): array{
        global $DB;
        $enrols = $DB->get_records_sql('SELECT id, courseid FROM {enrol} WHERE courseid = ?', [$int]);
        $enrolments = $DB->get_records('user_enrolments');
        $enrolledusers = [];
        //(courseid, userid)
        //get users who aren't suspended
        foreach($enrols as $enrol){
            foreach($enrolments as $enrolment){
                if($enrol->id == $enrolment->enrolid && $enrolment->status <> 1){
                    array_push($enrolledusers, [$enrol->courseid, $enrolment->userid]);
                }
            }
        }

        //(courseid, userid)
        //get users who are students
        $users1 = [];
        $contexts = $DB->get_records_sql('SELECT id FROM {context} WHERE instanceid = ?', [$int]);
        $roles = $DB->get_records('role_assignments');
        foreach($contexts as $context){
            foreach($roles as $role){
                if($role->contextid == $context->id && $role->roleid == 5){
                    array_push($users1, [$int, $role->userid]);
                }
            }
        }

        //(courseid, userid)
        $users2 = [];
        foreach($users1 as $u1){
            foreach($enrolledusers as $eusers){
                if($u1 == $eusers){
                    array_push($users2, [$u1[0], $u1[1]]);
                }
            }
        }
        $coursename = $DB->get_record_sql('SELECT fullname, id FROM {course} WHERE id = ?', [$int]);
        //(userid, username, coursename, courseid)
        $usernames = [];
        $users = $DB->get_records('user');
        foreach($users as $user){
            foreach($users2 as $enrolleduser){
                if($user->id == $enrolleduser[1]){
                    array_push($usernames, [$user->id, $user->firstname . ' ' . $user->lastname, $coursename->fullname, $coursename->id]);
                }
            }
        }

        return $usernames;
    }

    //get username from userid
    public function get_username($userid){
        global $DB;
        $username = $DB->get_record_sql('SELECT firstname, lastname FROM {user} WHERE id = ?', [$userid]);
        $username->username = $username->firstname .' '. $username->lastname;
        return $username;
    }

    //get course name from courseid
    public function get_coursename($courseid){
        global $DB;
        $coursename = $DB->get_record_sql('SELECT fullname FROM {course} WHERE id = ? ',[$courseid]);
        return $coursename;
    }

    //Check if the record exists in off_the_job_hours
    public function hours_exists($userid, $courseid){
        global $DB;
        if(!$DB->record_exists('off_the_job_hours', [$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $courseid])){
            $record = new stdClass();
            $record->userid = $userid;
            $record->courseid = $courseid;
            $record->learner = $this->get_username($userid)->username;
            $record->qualification = $this->get_coursename($courseid)->fullname;
            $DB->insert_record('off_the_job_hours', $record, false);
        }
        return;
    }

    //Get the hours id of a record
    public function hoursid($userid, $courseid){
        global $DB;
        $id = $DB->get_record_sql('SELECT id FROM {off_the_job_hours} WHERE userid = ? AND courseid = ?', [$userid, $courseid]);
        if(isset($id->id)){
            return $id->id;
        } else {
            return null;
        }
    }

    //Add new hours record to off_the_job_hours_info
    public function new_hours($userid, $courseid, $array){
        global $DB;
        $record = new stdClass();
        $record->hoursid = $this->hoursid($userid, $courseid);
        $record->date = $array[0][0];
        $record->activity = $array[0][1];
        $record->whatlink = $array[0][2];
        $record->impact = $array[0][3];
        $record->duration = $array[0][4];
        $record->initial = $array[0][5];
        $DB->insert_record('off_the_job_hours_info', $record, false);
        \local_offthejob\event\created_hours::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
    }

    //Get all hours records for specific user
    public function all_hours($userid, $courseid): array{
        global $DB;
        $hoursid = $this->hoursid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT * FROM {off_the_job_hours_info} WHERE hoursid = ?', [$hoursid]);
        $endarray = [];
        foreach($records as $record){
            array_push($endarray, [$record->date, $record->activity, $record->whatlink, $record->impact, $record->duration, $record->initial, $record->id]);
        }
        asort($endarray);
        $finalarray = [];
        $int = 0;
        foreach($endarray as $enarray){
            array_push($finalarray, [date('d-m-Y',$enarray[0]), $enarray[1], $enarray[2], $enarray[3], $enarray[4], $enarray[5], $int, $enarray[6]]);
            $int++;
        }
        return $finalarray;
    }

    //Get hours record by id
    public function get_hour($id): array{
        global $DB;
        $record = $DB->get_record_sql('SELECT * FROM {off_the_job_hours_info} WHERE id = ?',[$id]);
        $array = [$record->date, $record->activity, $record->whatlink, $record->impact, $record->duration, $record->initial];
        return $array;
    }

    //Delete hours record by id
    public function del_hour($id){
        global $DB;
        $hoursid = $DB->get_record_sql('SELECT hoursid FROM {off_the_job_hours_info} WHERE id = ?',[$id])->hoursid;
        $hours = $DB->get_record_sql('SELECT courseid, userid FROM {off_the_job_hours} WHERE id = ?',[$hoursid]);
        $DB->delete_records('off_the_job_hours_info', [$DB->sql_compare_text('id') => $id]);
        \local_offthejob\event\deleted_hours::create(array('context' => \context_course::instance($hours->courseid), 'relateduserid' => $hours->userid, 'courseid' => $hours->courseid))->trigger();
        return;
    }

    //Check if docs record exists and if not creates one
    public function docs_exists($userid, $courseid){
        global $DB;
        if(!$DB->record_exists('off_the_job_docs', [$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $courseid])){
            $record = new stdClass();
            $record->userid = $userid;
            $record->courseid = $courseid;
            $DB->insert_record('off_the_job_docs', $record, false);
        }
        return;
    }

    //Get docs id
    public function docsid($userid, $courseid){
        global $DB;
        $id = $DB->get_record_sql('SELECT id FROM {off_the_job_docs} WHERE userid = ? AND courseid = ?', [$userid, $courseid]);
        if(isset($id->id)){
            return $id->id;
        } else {
            return null;
        }
    }

    public function record_doc($array, $id){
        $record = new stdClass();
        $record->docsid = $id;
        $record->apprentice = $array[0];
        $record->reviewdate = $array[1];
        $record->standard = $array[2];
        $record->employerandstore = $array[3];
        $record->coach = $array[4];
        $record->managerormentor = $array[5];
        $record->progress = $array[6];
        $record->hours = $array[7];
        $record->recap = $array[8];
        $record->impact = $array[9];
        $record->details = $array[10];
        $record->detailsksb = $array[11];
        $record->detailimpact = $array[12];
        $record->todaymath = $array[13];
        $record->nextmath = $array[14];
        $record->todayeng = $array[15];
        $record->nexteng = $array[16];
        $record->todayict = $array[17];
        $record->nextict = $array[18];
        $record->activity = $array[19];
        $record->activityksb = $array[20];
        $record->agreedaction = $array[21];
        $record->employercomment = $array[22];
        $record->safeguarding = $array[23];
        $record->apprenticecomment = $array[24];
        $record->ntasigndate = $array[25];
        $record->learnsigndate = $array[26];
        $record->expectprogress = $array[27];
        $record->expecthours = $array[28];
        $record->alnsupport = $array[29];
        $record->progresscom = $array[30];
        $record->otjhcom = $array[31];
        return $record;
    }

    //Add record for a document to be saved as a draft
    public function draft_doc($userid, $courseid, $array){
        global $DB;
        $id = $this->docsid($userid, $courseid);
        $record = $this->record_doc($array, $id);
        if(!$DB->record_exists('off_the_job_docs_draft', [$DB->sql_compare_text('docsid') => $id])){
            $DB->insert_record('off_the_job_docs_draft', $record, false);
            \local_offthejob\event\created_mar_draft::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
        } else {
            $record->id = $DB->get_record_sql("SELECT id FROM {off_the_job_docs_draft} WHERE docsid = ?", [$id])->id;
            $DB->update_record('off_the_job_docs_draft', $record, false);
            \local_offthejob\event\updated_mar_draft::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
        }
        return;
    }

    //Add record for a document to be saved as complete
    public function save_doc($userid, $courseid, $array){
        global $DB;
        $id = $this->docsid($userid, $courseid);
        $record = $this->record_doc($array, $id);
        $DB->insert_record('off_the_job_docs_info', $record, false);
        \local_offthejob\event\created_mar::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
        return;
    }

    public function save_doc_learn($courseid, $array){
        global $DB;
        global $USER;
        $userid = $USER->id;
        $id = $this->docsid($userid, $courseid);
        $recordid = $DB->get_record_sql('SELECT id FROM {off_the_job_docs_info} WHERE id = ? AND docsid = ?',[$array[2], $id])->id;
        $record = new stdClass();
        $record->id = $recordid;
        $record->apprenticecomment = $array[0];
        $record->learnsigndate = $array[1];
        $DB->update_record('off_the_job_docs_info', $record, false);
        \local_offthejob\event\updated_mar_learner::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid, 'other' => $recordid))->trigger();
    }

    //Get draft for specfic user
    public function get_draft($userid, $courseid){
        global $DB;
        $id = $this->docsid($userid, $courseid);
        $record = $DB->get_record_sql("SELECT * FROM {off_the_job_docs_draft} WHERE docsid = ?", [$id]);
        $array = $this->record_to_array($record);
        return $array;
    }

    //Check if a draft exists for specfic userid and courseid
    public function draft_exists($userid, $courseid){
        global $DB;
        $id = $this->docsid($userid, $courseid);
        if(!$DB->record_exists('off_the_job_docs_draft', [$DB->sql_compare_text('docsid') => $id])){
            return false;
        }
        return true;
    }

    //Get records date and id
    public function records_date($userid, $courseid): array{
        global $DB;
        $id = $this->docsid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT id, reviewdate FROM {off_the_job_docs_info} WHERE docsid = ?',[$id]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->id, date('d-m-Y',$record->reviewdate)]);
        }
        return $array;
    }

    //Get document dependant on docs id
    public function get_doc_id($id){
        global $DB;
        $record = $DB->get_record_sql('SELECT * FROM {off_the_job_docs_info} WHERE id = ?',[$id]);
        $array = $this->record_to_array($record);
        return $array;
    }

    public function record_to_array($record): array{
        if($record->ntasigndate !== null){
            $record->ntasigndate = date('Y-m-d',$record->ntasigndate);
        }
        if($record->learnsigndate !== null){
            $record->learnsigndate = date('Y-m-d',$record->learnsigndate);
        }
        $array = [
            $record->apprentice,
            date('Y-m-d',$record->reviewdate),
            $record->standard,
            $record->employerandstore,
            $record->coach,
            $record->managerormentor,
            $record->progress,
            $record->hours,
            $record->recap,
            $record->impact,
            $record->details,
            $record->detailsksb,
            $record->detailimpact,
            $record->todaymath,
            $record->nextmath,
            $record->todayeng,
            $record->nexteng,
            $record->todayict,
            $record->nextict,
            $record->activity,
            $record->activityksb,
            $record->safeguarding,
            $record->agreedaction,
            $record->employercomment,
            $record->apprenticecomment,
            $record->ntasigndate,
            $record->learnsigndate,
            $record->expectprogress,
            $record->expecthours,
            $record->alnsupport,
            $record->progresscom,
            $record->otjhcom
        ];
        return $array;
    }

    //Get employer comment
    public function get_doc_id_ecom($id){
        global $DB;
        $pdf = $DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_info} WHERE id = ?',[$id])->employercomment;
        if(!empty($pdf)){
            $pdf = explode('/', $pdf);
            $pdf = end($pdf);
            return $pdf;
        }
    }

    //check if info employer comment is used in draft table
    public function check_employer_comment_info($id){
        global $DB;
        $pdf = $DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_info} WHERE id = ?',[$id])->employercomment;
        $record = $DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_draft} WHERE employercomment = ?',[$pdf])->employercomment;
        if($record !== null){
            return true;
        } else {
            return false;
        }
    }

    //get draft employer comment
    public function get_employer_comment_draft($userid, $courseid){
        global $DB;
        $id = $this->docsid($userid, $courseid);
        $pdf = $DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_draft} WHERE docsid = ?', [$id])->employercomment;
        if(!empty($pdf)){
            $pdf = explode('/', $pdf);
            $pdf = end($pdf);
            return $pdf;
        }
    }

    //check if draft employer comment is used in info table
    public function check_employer_comment_draft($userid, $courseid){
        global $DB;
        $id = $this->docsid($userid, $courseid);
        $ecom = $DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_draft} WHERE docsid = ?',[$id])->employercomment;
        $record = $DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_info} WHERE employercomment = ?',[$ecom])->employercomment;
        if($record !== null){
            return true;
        } else {
            return false;
        }
    }

    //Delte doc
    public function del_doc_id($id){
        global $DB;
        $docsid = $DB->get_record_sql('SELECT docsid FROM {off_the_job_docs_info} WHERE id = ?',[$id])->docsid;
        $record = $DB->get_record_sql('SELECT userid, courseid FROM {off_the_job_docs} WHERE id = ?',[$docsid]);
        $DB->delete_records('off_the_job_docs_info', [$DB->sql_compare_text('id') => $id]);
        \local_offthejob\event\deleted_mar::create(array('context' => \context_course::instance($record->courseid), 'relateduserid' => $record->userid, 'courseid' => $record->courseid))->trigger();
        return;
    }

    //get students enrollments in apprenticeships
    public function learner_enrol(){
        global $USER;
        $user = [$USER->id, ("$USER->firstname "."$USER->lastname")];
        global $DB;

        //Get enrolments for user
        $enrolments = $DB->get_records_sql('SELECT * FROM {user_enrolments} WHERE userid = ?', [$user[0]]);
        $enrols = $DB->get_records('enrol');
        $array = [];
        foreach($enrolments as $enrolment){
            foreach($enrols as $enrol){
                if($enrol->id == $enrolment->enrolid && $enrol->status !== 1){
                    array_push($array, [$enrol->courseid, $enrolment->userid]);
                }
            }
        }

        //get role assignments for user 
        $roleassigns = $DB->get_records_sql('SELECT * FROM {role_assignments} WHERE userid = ?', [$user[0]]);
        $contexts = $DB->get_records('context');
        $array2 = [];
        foreach($roleassigns as $roleassign){
            foreach($contexts as $context){
                if($roleassign->contextid == $context->id && $roleassign->roleid == 5){
                    array_push($array2, [$context->instanceid, $roleassign->userid]);
                }
            }
        }

        //Compare two arrays
        $courses = [];
        foreach($array as $arr){
            foreach($array2 as $arr2){
                if($arr == $arr2){
                    array_push($courses, $arr2[0]);
                }
            }
        }

        //Get category id
        $categoryid = $DB->get_record_sql('SELECT id FROM {course_categories} WHERE name = ?',['Apprenticeships']);

        //get courses 
        $coursestable = $DB->get_records('course');
        $endarray = [];
        foreach($coursestable as $cour){
            foreach($courses as $cours){
                if($cour->id == $cours && $cour->category == $categoryid->id){
                    array_push($endarray, [$cour->fullname, 'Apprenticeship', $cour->id]);
                }
            }
        }
        asort($endarray);
        return $endarray;
    }

    //Get current users hours log: input course id
    public function user_hours_log($courseid){
        global $DB;
        global $USER;
        $user = $USER->id;
        $hours = $DB->get_record_sql('SELECT id FROM {off_the_job_hours} WHERE userid = ? and courseid = ?', [$user, $courseid]);
        $info = $DB->get_records_sql('SELECT * FROM {off_the_job_hours_info} WHERE hoursid = ?', [$hours->id]);
        $array = [];
        $int = 0;
        foreach($info as $inf){
            array_push($array, [$inf->date, $inf->activity, $inf->whatlink, $inf->impact, $inf->duration, $inf->initial, $int, $inf->id]);
            $int++;
        }
        asort($array);
        $array2 = [];
        $int = 0;
        foreach($array as $arr){
            array_push($array2, [date('d-m-Y',$arr[0]), $arr[1], $arr[2], $arr[3], $arr[4], $arr[5], $int, $arr[7]]);
            $int++;
        }
        return $array2;
    }

    //function for checking if a record of them exists
    public function docs_exists_learn($courseid){
        global $USER;
        global $DB;
        $user = $USER->id;
        if(!$DB->record_exists('off_the_job_docs', [$DB->sql_compare_text('userid') => $user, $DB->sql_compare_text('courseid') => $courseid])){
            $record = new stdClass();
            $record->userid = $user;
            $record->courseid = $courseid;
            $DB->insert_record('off_the_job_docs', $record, false);
        }
        return;
    }

    //check if a draft exists
    public function draft_exists_learn($courseid){
        global $USER;
        global $DB;
        $user = $USER->id;
        $id = $this->docsid($user, $courseid);
        if(!$DB->record_exists('off_the_job_docs_draft', [$DB->sql_compare_text('docsid') => $id])){
            return false;
        }
        return true;
    }

    //Get records date and id
    public function records_date_learn($courseid): array{
        global $USER;
        global $DB;
        $user = $USER->id;
        $id = $this->docsid($user, $courseid);
        $records = $DB->get_records_sql('SELECT id, reviewdate FROM {off_the_job_docs_info} WHERE docsid = ?',[$id]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->id, date('d-m-Y',$record->reviewdate)]);
        }
        return $array;
    }

    //Get draft for specfic user
    public function get_draft_learn($courseid){
        global $USER;
        global $DB;
        $user = $USER->id;
        $id = $this->docsid($user, $courseid);
        $record = $DB->get_record_sql("SELECT * FROM {off_the_job_docs_draft} WHERE docsid = ?", [$id]);
        $array = $this->record_to_array($record);
        return $array;
    }

    //Get document dependant on docs id and courseid
    public function get_doc_id_learn($id, $courseid){
        global $USER;
        global $DB;
        $user = $USER->id;
        $docsid = $this->docsid($user, $courseid);
        $record = $DB->get_record_sql('SELECT * FROM {off_the_job_docs_info} WHERE id = ? AND docsid = ?',[$id, $docsid]);
        $array = $this->record_to_array($record);
        return $array;
    }   

    //Delete doc dependant on id and courseid
    public function del_doc_id_learn($id, $courseid){
        global $USER;
        global $DB;
        $user = $USER->id;
        $docsid = $this->docsid($user, $courseid);
        $DB->delete_records('off_the_job_docs_info', [$DB->sql_compare_text('id') => $id, $DB->sql_compare_text('docsid') => $docsid]);
        return;
    }


    //Check if off the job setup exists for user and course
    public function setup_exists($userid, $courseid){
        global $DB;
        if(!$DB->record_exists('off_the_job_setup', [$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $courseid])){
            return false;
        }
        return true;

    }

    //Off the Job setup for db
    public function setup($userid, $courseid, $array){
        global $DB;
        $record = new stdClass();
        $record->userid = $userid;
        $record->courseid = $courseid;
        $record->totalmonths = $array[0];
        $record->otjhours = $array[1];
        $record->employerorstore = $array[2];
        $record->coach = $array[3];
        $record->managerormentor = $array[4];
        $record->ntasign = $array[5];
        $record->startdate = $array[6];
        $record->hoursperweek = $array[7];
        $record->annuallw = $array[8];
        $record->planfilename = $array[9];

        global $USER;
        $teachid = $USER->id;
        $record->teachid = $teachid;

        $DB->insert_record('off_the_job_setup', $record, false);
        \local_offthejob\event\created_setup::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
    }

    //Get setup data
    public function setup_data($userid, $courseid){
        global $DB;
        $record = $DB->get_record_sql('SELECT * FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?', [$userid, $courseid]);
        return $record;
    }

    //Get Total Off the job hours for specific learner and course
    public function get_cumulative_hours($userid, $courseid): int{
        global $DB;
        $hoursid = $this->hoursid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT duration FROM {off_the_job_hours_info} WHERE hoursid = ?', [$hoursid]);
        $total = 0;
        foreach($records as $record){
            $total = $total + $record->duration;
        }
        return $total;
    }

    //Get the percentage of hours completed : used for teacher page
    public function get_percent_hours($userid, $courseid): float{
        $total = 0;
        global $DB;
        $hours = $this->get_cumulative_hours($userid, $courseid);
        $totalhours = $DB->get_record_sql('SELECT otjhours FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?', [$userid, $courseid]);
        $total = floatval(number_format(($hours / $totalhours->otjhours) * 100, 0, '.',' '));
        if($total < 0){
            $total = 0;
        }
        return $total;
    }
    
    //Get expected percentage for learner : teacher page
    public function get_percent_expect($userid, $courseid){
        global $DB;
        $setupdb = $DB->get_record_sql('SELECT totalmonths, otjhours, startdate FROM {off_the_job_setup} WHERE userid = ? and courseid = ?', [$userid, $courseid]);
        $average = ($setupdb->otjhours / $setupdb->totalmonths) / 4;
        $date = date('U');
        $date = $date - $setupdb->startdate;
        $weeks = round($date / 604800);
        $expected = $average * $weeks;
        $expected = floatval(number_format(($expected / $setupdb->otjhours) * 100, 0, '.',' '));
        if($expected < 0){
            $expected = 0;
        }
        return $expected;
    }

    //Check if the setup exists : learner page
    public function setup_exists_learn($courseid){
        global $DB;
        global $USER;
        $user = $USER->id;
        if(!$DB->record_exists('off_the_job_setup', [$DB->sql_compare_text('userid') => $user, $DB->sql_compare_text('courseid') => $courseid])){
            return false;
        }
        return true;
    }

    //Check for learner signature
    public function learn_sign_exists($courseid){
        global $DB;
        global $USER;
        $user = $USER->id;
        $record = $DB->get_record_sql('SELECT learnersign FROM {off_the_job_setup} WHERE courseid = ? AND userid = ?', [$courseid, $user]);
        if(empty($record->learnersign)){
            return false;
        }
        return true;
    }

    //Add learner signature
    public function learn_sign($courseid, $url){
        global $DB;
        global $USER;
        $user = $USER->id;
        $current = $DB->get_record_sql('SELECT * FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?', [$user, $courseid]);
        $record = new stdClass();
        $record->id = $current->id;
        $record->learnersign = $url;
        $DB->update_record('off_the_job_setup', $record);
        return;
    }

    //Add nta signature
    public function nta_sign($userid, $courseid, $url){
        global $DB;
        global $USER;
        $user = $USER->id;
        $current = $DB->get_record_sql('SELECT id FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?',[$userid, $courseid])->id;
        $record = new stdClass();
        $record->id = $current;
        $record->teachid = $user;
        $record->ntasign = $url;
        $DB->update_record('off_the_job_setup', $record);
        \local_offthejob\event\updated_coach_signature::create(array("context" => \context_course::instance($courseid), "relateduserid" => $userid, 'courseid' => $courseid))->trigger();
    }

    //Get the percentage of the number of hours completed for a learner
    public function get_percent_hours_learn($courseid){
        global $DB;
        global $USER;
        $user = $USER->id;
        $totalhours = $DB->get_record_sql('SELECT otjhours FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?', [$user, $courseid]);
        $hoursid = $this->hoursid($user, $courseid);
        $sumhours = $DB->get_records_sql('SELECT duration FROM {off_the_job_hours_info} WHERE hoursid = ?', [$hoursid]);
        $shour = 0;
        foreach($sumhours as $sumhour){
            $shour = $shour + $sumhour->duration;
        }
        $percent = floatval(number_format(($shour / $totalhours->otjhours) * 100, 0, '.',' '));
        if($percent < 0){
            $percent = 0;
        }
        return $percent;
    }

    public function get_cumulative_hours_learn($courseid){
        global $DB;
        global $USER;
        $userid = $USER->id;
        $hoursid = $this->hoursid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT duration FROM {off_the_job_hours_info} WHERE hoursid = ?',[$hoursid]);
        $hours = 0;
        foreach($records as $record){
            $hours = $hours + $record->duration;
        }
        return $hours;
    }   

    //Get expected percentage for learner : learner page
    public function get_percent_expect_learn($courseid){
        global $DB;
        global $USER;
        $user = $USER->id;
        $setupdb = $DB->get_record_sql('SELECT totalmonths, otjhours, startdate FROM {off_the_job_setup} WHERE userid = ? and courseid = ?', [$user, $courseid]);
        $average = ($setupdb->otjhours / $setupdb->totalmonths) / 4;
        $date = date('U');
        $date = $date - $setupdb->startdate;
        $weeks = round($date / 604800);
        $expected = $average * $weeks;
        $expected = floatval(number_format(($expected / $setupdb->otjhours) * 100, 0, '.',' '));
        if($expected < 0){
            $expected = 0;
        }
        return $expected;
    }

    //Get nta signature string : teacher page
    public function ntasign($userid, $courseid){
        global $DB;
        $record = $DB->get_record_sql('SELECT ntasign FROM {off_the_job_setup} WHERE userid = ? and courseid = ?', [$userid, $courseid]);
        return $record->ntasign;
    }

    //Get learner signature : teacher page
    public function learnsigned($userid, $courseid){
        global $DB;
        $record = $DB->get_record_sql('SELECT learnersign FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?', [$userid, $courseid]);
        return $record->learnersign;
    }

    //Get learner signature string : learner page
    public function learnsign($courseid){
        global $DB;
        global $USER;
        $userid = $USER->id;
        $record = $DB->get_record_sql('SELECT learnersign FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?', [$userid, $courseid]);
        return $record->learnersign;
    }

    //Get nta signature string : learner page
    public function ntasign_learn($courseid){
        global $DB;
        global $USER;
        $userid = $USER->id;
        $record = $DB->get_record_sql('SELECT ntasign FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?', [$userid, $courseid]);
        return $record->ntasign;
    }

    //Get current username : learner page
    public function user_username(){
        global $USER;
        $username = $USER->firstname .' ' . $USER->lastname;
        return $username;
    }

    //Get OTJ info : Teacher Page
    public function otj_info($userid, $courseid){
        global $DB;
        $info = $DB->get_record_sql('SELECT totalmonths, otjhours, hoursperweek, annuallw FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?',[$userid, $courseid]);
        $hoursid = $this->hoursid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT duration FROM {off_the_job_hours_info} WHERE hoursid = ?',[$hoursid]);
        $duration = 0;
        foreach($records as $record){
            $duration = $duration + $record->duration;
        }
        $totalleft = $info->otjhours - $duration;
        $weeksonp = $info->totalmonths * 4.34;
        $array = [$info->otjhours, $totalleft, round($info->hoursperweek * 0.2), $info->totalmonths, round($weeksonp), $info->annuallw];
        return $array;
    }

    //Get OTJ info : learner page
    public function otj_info_learn($courseid){
        global $DB;
        global $USER;
        $userid = $USER->id;
        $info = $DB->get_record_sql('SELECT totalmonths, otjhours, hoursperweek, annuallw FROM {off_the_job_setup} WHERE userid = ? and courseid = ?',[$userid, $courseid]);
        $totalmonths = $info->totalmonths;
        $hoursperweek = $info->hoursperweek;
        $otjhours = $info->otjhours;
        $hoursid = $this->hoursid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT duration FROM {off_the_job_hours_info} WHERE hoursid = ?',[$hoursid]);
        $duration = 0;
        foreach($records as $record){
            $duration = $duration + $record->duration;
        }
        $totalleft = $otjhours - $duration;
        $weeksonp = $totalmonths * 4.34;
        $array = [$otjhours, $totalleft, round($hoursperweek*0.2), $totalmonths, round($weeksonp), $info->annuallw];
        return $array;
    }

    //Get setup data for learner
    public function setup_data_learn($courseid){
        global $DB;
        global $USER;
        $userid = $USER->id;
        $record = $DB->get_record_sql('SELECT employerorstore, coach, managerormentor FROM {off_the_job_setup} WHERE userid = ? and courseid = ?', [$userid, $courseid]);
        return $record;
    }

    //Get assignment completion : learner page
    public function assign_comp_learn($courseid){
        global $DB;
        global $USER;
        $userid = $USER->id;
        $records = $DB->get_records_sql('SELECT id FROM {course_modules} WHERE course = ? AND completion != ?',[$courseid, 0]);
        $completions = $DB->get_records_sql('SELECT coursemoduleid FROM {course_modules_completion} WHERE userid = ? AND completionstate = ?',[$userid, 1]);
        $complete = 0;
        $total = 0;
        foreach($records as $record){
            foreach($completions as $completion){
                if($record->id == $completion->coursemoduleid){
                    $complete++;
                }
            }
            $total++;
        }
        $percent = round(($complete / $total) *100);
        $totalmonth = $DB->get_record_sql('SELECT totalmonths, startdate FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?', [$userid, $courseid]);
        $expected = ($total / $totalmonth->totalmonths) / 4;
        $startdate = $totalmonth->startdate;
        $date = date('U');
        $date = $date - $startdate;
        $weeks = round($date / 604800);
        $expected = round((($expected * $weeks) / $total) *100);
        if($expected < 0){
            $expected = 0;
        }
        $array = [$percent, $expected];
        return $array;
    }

    //Function for getting data for the module table : learner page
    public function module_table_learn($courseid){
        global $DB;
        global $USER;
        $userid = $USER->id;
        $cmodules = $DB->get_records_sql('SELECT id, module FROM {course_modules} WHERE course = ? AND completion != ?',[$courseid, 0]);
        $modules = $DB->get_records('modules');
        $array = [];
        foreach($cmodules as $cmod){
            foreach($modules as $mod){
                if($cmod->module == $mod->id){
                    array_push($array, [$cmod->id, $mod->name]);
                }
            }
        }
        $modcomps = $DB->get_records_sql('SELECT coursemoduleid FROM {course_modules_completion} WHERE userid = ? AND completionstate = ?', [$userid, 1]);
        //Get course modules name and id
        $info = get_fast_modinfo($courseid);
        $modnames = [];
        foreach($info->cms as $inf){
            if($inf->name !== 'Announcements'){
                array_push($modnames, [$inf->id, $inf->name]);
            }
        }
        //
        $namedarray = [];
        foreach($array as $arr){
            foreach($modnames as $mname){
                if($arr[0] == $mname[0]){
                    array_push($namedarray, [$arr[0], $mname[1], $arr[1]]);
                }
            }
        }
        $finalarray = [];
        foreach($namedarray as $namedarr){
            $int = 0;
            foreach($modcomps as $modcomp){
                if($modcomp->coursemoduleid == $namedarr[0]){
                    $int++;
                }
            }
            if($int == 1){
                array_push($finalarray, [$namedarr[1], $namedarr[2], 'Complete']);
            } else {
                array_push($finalarray, [$namedarr[1], $namedarr[2], 'Incomplete']);
            }
        }
        return $finalarray;
    }

    //Get assignment completion : teacher page
    public function assign_comp($userid, $courseid){
        global $DB;
        $records = $DB->get_records_sql('SELECT id FROM {course_modules} WHERE course = ? and completion != ?',[$courseid, 0]);
        $completions = $DB->get_records_sql('SELECT coursemoduleid FROM {course_modules_completion} WHERE userid = ? AND completionstate = ?', [$userid, 1]);
        $complete = 0;
        $total = 0;
        foreach($records as $record){
            foreach($completions as $completion){
                if($record->id == $completion->coursemoduleid){
                    $complete++;
                }
            }
            $total++;
        }
        $percent = round(($complete / $total) * 100);
        $totalmonth = $DB->get_record_sql('SELECT totalmonths, startdate FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?', [$userid, $courseid]);
        $expected = ($total / $totalmonth->totalmonths) / 4;
        $startdate = $totalmonth->startdate;
        $date = date('U');
        $date = $date - $startdate;
        $weeks = round($date / 604800);
        $expected = round((($expected * $weeks) / $total) * 100);
        if($expected < 0){
            $expected = 0;
        }
        $array = [$percent, $expected];
        return $array;
    }

    //Get data for the module table : teacher page
    public function module_table($userid, $courseid){
        global $DB;
        $cmodules = $DB->get_records_sql('SELECT id, module FROM {course_modules} WHERE course = ? AND completion != ?', [$courseid, 0]);
        $modules = $DB->get_records('modules');
        $array = [];
        foreach($cmodules as $cmod){
            foreach($modules as $module){
                if($cmod->module == $module->id){
                    array_push($array, [$cmod->id, $module->name]);
                }
            }
        }
        $modcomps = $DB->get_records_sql('SELECT coursemoduleid FROM {course_modules_completion} WHERE userid = ? and completionstate = ?', [$userid, 1]);
        //Get course modules name and id
        $info = get_fast_modinfo($courseid);
        $modnames = [];
        foreach($info->cms as $inf){
            if($inf->name !== 'Announcements'){
                array_push($modnames, [$inf->id, $inf->name]);
            }
        }
        //
        $namedarray = [];
        foreach($array as $arr){
            foreach($modnames as $mname){
                if($arr[0] == $mname[0]){
                    array_push($namedarray, [$arr[0], $mname[1], $arr[1]]);
                }
            }
        }
        $finalarray = [];
        foreach($namedarray as $namedarr){
            $int = 0;
            foreach($modcomps as $modcomp){
                if($modcomp->coursemoduleid == $namedarr[0]){
                    $int++;
                }
            }
            if($int == 1){
                array_push($finalarray, [$namedarr[1], $namedarr[2], 'Complete']);
            } else {
                array_push($finalarray, [$namedarr[1], $namedarr[2], 'Incomplete']);
            }
        }
        return $finalarray;
    }

    //Get training plan names, and file name
    public function training_plans(){
        global $CFG;
        //Get files
        $files = scandir($CFG->dirroot.'/local/offthejob/templates/json');
        //Remove first two elements
        unset($files[0]);
        unset($files[1]);
        //remove null values
        $files = array_values($files);
        $filesarray = [];
        foreach($files as $file){
            $json = file_get_contents($CFG->dirroot.'/local/offthejob/templates/json/'.$file);
            $json = json_decode($json);
            array_push($filesarray, [$json->name, $file]);
        }
        return $filesarray;
    }

    //Get training plan for specific user and course : teacher page
    public function user_plan_data($userid, $courseid){
        global $DB;
        $record = $DB->get_record_sql('SELECT planfilename, employerorstore, totalmonths, startdate, otjhours, hoursperweek, annuallw FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?', [$userid, $courseid]); 
        return $record;
    }

    //Check if plan record exists
    public function plan_exists($userid, $courseid){
        global $DB;
        if(!$DB->record_exists('off_the_job_plans', [$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $courseid])){
            return false;
        } else {
            return true;
        }
    }

    //Used for creating a new plan
    public function create_plan($userid, $courseid, $leftarray, $progarray, $fsarray, $modarray){
        global $DB;
        if($DB->record_exists('off_the_job_plans', [$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $courseid])){
            return;
        }
        $leftobject = new stdClass();
        $leftobject->userid = $userid;
        $leftobject->courseid = $courseid;
        $leftobject->name = $leftarray[0];
        $leftobject->employer = $leftarray[1];
        $leftobject->startdate = $leftarray[2];
        $leftobject->plannedendd = $leftarray[3];
        $leftobject->lengthoprog = $leftarray[4];
        $leftobject->otjh = $leftarray[5];
        $leftobject->epao = $leftarray[6];
        $leftobject->fundsource = $leftarray[7];
        $leftobject->bskbrm = $leftarray[8];
        $leftobject->bskre = $leftarray[9];
        $leftobject->learnstyle = $leftarray[10];
        $leftobject->sslearnr = $leftarray[11];
        $leftobject->ssemployr = $leftarray[12];
        $leftobject->apprenhpw = $leftarray[13];
        $leftobject->weekop = $leftarray[14];
        $leftobject->annuall = $leftarray[15];
        $leftobject->pdhours = $leftarray[16];
        $leftobject->areaostren = $leftarray[17];
        $leftobject->longtgoal = $leftarray[18];
        $leftobject->shorttgoal = $leftarray[19];
        $leftobject->iag = $leftarray[20];
        $leftobject->recopl = $leftarray[21];
        $leftobject->addsa = $leftarray[22];
        $DB->insert_record('off_the_job_plans', $leftobject, false);

        $planid = $this->planid($userid, $courseid);

        foreach($progarray as $progarr){
            $progobject = new stdClass();
            $progobject->plansid = $planid;
            $progobject->prpos = $progarr[2];
            $progobject->prtor = $progarr[0];
            $progobject->prpr = $progarr[1];
            $DB->insert_record('off_the_job_plans_pr', $progobject, false);
        }

        foreach($fsarray as $fsarr){
            $fsobject = new stdClass();
            $fsobject->plansid = $planid;
            $fsobject->fsname = $fsarr[0];
            $fsobject->fslevel = $fsarr[1];
            $fsobject->fsmod = $fsarr[2];
            $fsobject->fssd = $fsarr[3];
            $fsobject->fsped = $fsarr[4];
            $fsobject->fsusd = $fsarr[5];
            $fsobject->fsuped = $fsarr[6];
            $fsobject->fspos = $fsarr[7];
            $DB->insert_record('off_the_job_plans_fs', $fsobject, false);
        }

        $otjhsum = 0;
        $position = [];
        foreach($modarray as $modarr){
            $modobject = new stdClass();
            $modobject->plansid = $planid;
            $modobject->modname = $modarr[0];
            $modobject->modpsd = $modarr[1];
            $modobject->modped = $modarr[2];
            $modobject->modw = $modarr[3];
            $modobject->modotjh = $modarr[4];
            $otjhsum += round($modarr[4]);
            $modobject->modmod = $modarr[5];
            $modobject->modotjt = $modarr[6];
            $modobject->modpos = $modarr[7];
            $DB->insert_record('off_the_job_plans_modules', $modobject, false);
            $position = $modarr[7];
        }
        $otjhtotal = $leftarray[5];
        if($otjhsum !== $otjhtotal){
            $recordid = $DB->get_record_sql('SELECT id FROM {off_the_job_plans_modules} WHERE plansid = ? AND modpos = ?', [$planid, $position])->id;
            if(($otjhtotal - $otjhsum) == 1){
                $modobject =  new stdClass();
                $modobject->id = $recordid;
                $modobject->plansid = $planid;
                $modobject->modname = end($modarray)[0];
                $modobject->modpsd = end($modarray)[1];
                $modobject->modped = end($modarray)[2];
                $modobject->modw = end($modarray)[3];
                $modobject->modotjh = end($modarray)[4] + 1;
                $modobject->modmod = end($modarray)[5];
                $modobject->modotjt = end($modarray)[6];
                $modobject->modpos = end($modarray)[7];
                $DB->update_record('off_the_job_plans_modules', $modobject, false);
            } elseif(($otjhtotal - $otjhsum) == 2){
                $modobject =  new stdClass();
                $modobject->id = $recordid;
                $modobject->plansid = $planid;
                $modobject->modname = end($modarray)[0];
                $modobject->modpsd = end($modarray)[1];
                $modobject->modped = end($modarray)[2];
                $modobject->modw = end($modarray)[3];
                $modobject->modotjh = end($modarray)[4] + 2;
                $modobject->modmod = end($modarray)[5];
                $modobject->modotjt = end($modarray)[6];
                $modobject->modpos = end($modarray)[7];
                $DB->update_record('off_the_job_plans_modules', $modobject, false);
            }
        }
        \local_offthejob\event\created_plan::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
    }

    //Get plan id
    public function planid($userid, $courseid){
        global $DB;
        $planid = $DB->get_record_sql('SELECT id FROM {off_the_job_plans} WHERE userid = ? AND courseid = ?',[$userid, $courseid]);
        return $planid->id;
    }

    //Get single field info for Plan
    public function plan_single($userid, $courseid){
        global $DB;
        $record = $DB->get_record_sql('SELECT * FROM {off_the_job_plans} WHERE userid = ? AND courseid = ?',[$userid, $courseid]);
        $array = [
            $record->name,
            $record->employer,
            $record->startdate,
            $record->plannedendd,
            $record->lengthoprog,
            $record->otjh,
            $record->epao,
            $record->fundsource,
            $record->bskbrm,
            $record->bskre,
            $record->learnstyle,
            $record->sslearnr,
            $record->ssemployr,
            $record->apprenhpw,
            $record->weekop,
            $record->annuall,
            $record->pdhours,
            $record->areaostren,
            $record->longtgoal,
            $record->shorttgoal,
            $record->iag,
            $record->recopl,
            $record->addsa,
        ];
        return $array;
    }

    //Get modules for specific user and course for training plan update modules in db as well
    public function plan_modules($userid, $courseid){
        global $DB;
        $planid = $this->planid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT * FROM {off_the_job_plans_modules} WHERE plansid = ?', [$planid]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->modpos, $record->modname, $record->modw, $record->modotjh, $record->modmod, $record->modpsd, $record->modped, $record->modotjt, $record->modrsd, $record->modred, $record->modaotjhc, $record->id]);
        }
        asort($array);
        $lastarray = [];
        $hoursid = $DB->get_record_sql('SELECT id FROM {off_the_job_hours} WHERE userid = ? AND courseid = ?',[$userid, $courseid])->id;
        foreach($array as $arr){
            $hoursinfo = $DB->get_records_sql('SELECT duration FROM {off_the_job_hours_info} WHERE hoursid = ? and whatlink = ?',[$hoursid, $arr[1]]);
            $num = 0;
            foreach($hoursinfo as $hoursinf){
                $num = $num + $hoursinf->duration;
            }
            $updating = new stdClass();
            $updating->id = $arr[11];
            $updating->modaotjhc = $num;
            $DB->update_record('off_the_job_plans_modules', $updating, false);
            array_push($lastarray, [$arr[1], $arr[2], $arr[3], $arr[4], $arr[0], $arr[5], $arr[6], $arr[7], $arr[8], $arr[9], $num]);
        }
        return $lastarray;
    }

    //Get funtional skills for specific user and course for training plan
    public function plan_fs($userid, $courseid){
        global $DB;
        $planid = $this->planid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT * FROM {off_the_job_plans_fs} WHERE plansid = ?',[$planid]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->fspos, $record->fsname, $record->fslevel, $record->fsmod, $record->fssd, $record->fsped, $record->fsusd, $record->fsuped, $record->fsaead, $record->fsaed]);
        }
        asort($array);
        $finalarray = [];
        foreach($array as $arr){
            array_push($finalarray, [$arr[1], $arr[3], $arr[0], $arr[2], $arr[4], $arr[5], $arr[6], $arr[7], $arr[8], $arr[9]]);
        }
        return $finalarray;
    }

    //Get progress revies for specific user and course for training plan
    public function plan_prog($userid, $courseid){
        global $DB;
        $planid = $this->planid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT * FROM {off_the_job_plans_pr} WHERE plansid = ?',[$planid]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->prpos, $record->prtor, $record->prpr, $record->prar]);
        }
        asort($array);
        $finalarray = [];
        foreach($array as $arr){
            array_push($finalarray, [$arr[1], $arr[2], $arr[3], $arr[0]]);
        }
        return $finalarray;
    }

    //Get changes log for specific user and course for training plan
    public function plan_changeslog($userid, $courseid){
        global $DB;
        $planid = $this->planid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT * FROM {off_the_job_plans_log} WHERE plansid = ?',[$planid]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->dateofc, $record->log]);
        }
        asort($array);
        return $array;
    }

    //Update Training plan for specific user and course
    public function update_plan($userid, $courseid, $modarray, $fsarray, $progarray, $logsarray, $leftarray){
        global $DB;
        //Get plan id
        $planid = $this->planid($userid, $courseid);
        //Update single data fields
        if(!empty($leftarray)){
            $record = new stdClass();
            $record->id = $planid;
            $record->areaostren = $leftarray[0];
            $record->longtgoal = $leftarray[1];
            $record->shorttgoal = $leftarray[2];
            $record->iag = $leftarray[3];
            $DB->update_record('off_the_job_plans', $record, false);
        }
        //update module values
        foreach($modarray as $modarr){
            $modobject = new stdClass();
            $modobject->id = $DB->get_record_sql('SELECT id FROM {off_the_job_plans_modules} WHERE plansid = ? AND modpos = ?', [$planid, $modarr[0]])->id;
            $modobject->plansid = $planid;
            $modobject->modred = $modarr[1];
            $modobject->modotjt = $modarr[2];
            $modobject->modrsd = $modarr[4];
            $DB->update_record('off_the_job_plans_modules', $modobject, false);
        }
        //update functional skills values
        $int = 0;
        foreach($fsarray as $fsarr){
            $fsobject = new stdClass();
            $fsobject->id = $DB->get_record_sql('SELECT id FROM {off_the_job_plans_fs} WHERE plansid = ? and fspos = ?', [$planid, $fsarr[0]])->id;
            if($int !== 2){
                $fsobject->fsaed = $fsarr[1];
                $fsobject->fsaead = $fsarr[2];
                $fsobject->fsusd = $fsarr[3];
                $fsobject->fsuped = $fsarr[4];
            } elseif($int == 2){
                $fsobject->fsaed = $fsarr[1];
                $fsobject->fsaead = $fsarr[2];
                $fsobject->fsusd = $fsarr[3];
                $fsobject->fsuped = $fsarr[4];
                $fsobject->fslevel = $fsarr[5];
                $fsobject->fssd = $fsarr[6];
                $fsobject->fsped = $fsarr[7];
            }
            $DB->update_record('off_the_job_plans_fs', $fsobject, false);
            $int++;
        }
        //input/update progress values
        foreach($progarray as $progarr){
            $exists = $DB->record_exists('off_the_job_plans_pr', [$DB->sql_compare_text('plansid') => $planid, $DB->sql_compare_text('prpos') => $progarr[3]]);
            $probject = new stdClass();
            $probject->prpr = $progarr[1];
            $probject->prar = $progarr[2];
            if($exists == true){
                $probject->id = $DB->get_record_sql('SELECT id FROM {off_the_job_plans_pr} WHERE plansid = ? AND prpos = ?',[$planid, $progarr[3]])->id;
                $probject->prtor = $progarr[0];
                $DB->update_record('off_the_job_plans_pr', $probject, false);
            } elseif($exists == false){
                $probject->plansid = $planid;
                $probject->prpos = $progarr[3];
                $probject->prtor = $progarr[0];
                $DB->insert_record('off_the_job_plans_pr', $probject, false);
            }
        }
        //input/update values for changes log
        foreach($logsarray as $logsarr){
            $exists = $DB->record_exists('off_the_job_plans_log', [$DB->sql_compare_text('plansid') => $planid, $DB->sql_compare_text('dateofc') => $logsarr[0]]);
            $logobject = new stdClass();
            $logobject->dateofc = $logsarr[0];
            $logobject->log = $logsarr[1];
            if($exists == true){
                $logobject->id = $DB->get_record_sql('SELECT id FROM {off_the_job_plans_log} WHERE plansid = ? AND dateofc = ?',[$planid, $logsarr[0]])->id;
                $DB->update_record('off_the_job_plans_log', $logobject, false);
            } elseif($exists == false){
                $logobject->plansid = $planid;
                $DB->insert_record('off_the_job_plans_log', $logobject, false);
            }
        }
        \local_offthejob\event\updated_plan::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
    }

    //Get plan modules for specific user and course : teacher page
    public function plan_json_modules($userid, $courseid){
        global $DB;
        global $CFG;
        $planfilename = $DB->get_record_sql('SELECT planfilename FROM {off_the_job_setup} WHERE userid = ? and courseid = ?',[$userid, $courseid])->planfilename;
        $json = file_get_contents($CFG->dirroot.'/local/offthejob/templates/json/'.$planfilename);
        $json = json_decode($json);
        $array = $json->modules;
        $finarray = [];
        foreach($array as $arr){
            array_push($finarray, array($arr->name));
        }
        return $finarray;
    }

    //Get plan modules for specific user and course : learner page
    public function plan_json_modules_learn($courseid){
        global $DB;
        global $USER;
        global $CFG;
        $userid = $USER->id;
        $planfilename = $DB->get_record_sql('SELECT planfilename FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?',[$userid, $courseid])->planfilename;
        $json = file_get_contents($CFG->dirroot.'/local/offthejob/templates/json/'.$planfilename);
        $json = json_decode($json);
        $array = $json->modules;
        $finarray = [];
        foreach($array as $arr){
            array_push($finarray, array($arr->name));
        }
        return $finarray;
    }

    //Get all users with a setup : admin page
    public function setted_users(){
        global $DB;
        $records = $DB->get_records_sql('SELECT id, userid, courseid FROM {off_the_job_setup}');
        $array = [];
        foreach($records as $record){
            $username = $this->get_username($record->userid)->username;
            $coursename = $this->get_coursename($record->courseid)->fullname;
            array_push($array, [$username, $record->userid, $coursename, $record->courseid]);
        }
        return $array;
    }

    //Get signatures : admin page
    public function admin_signatures($userid, $courseid){
        global $DB;
        $record = $DB->get_record_sql('SELECT learnersign, ntasign FROM {off_the_job_setup} WHERE userid = ? and courseid = ?',[$userid, $courseid]);
        return $record;
    }

    //Get intial setup data : admin page
    public function admin_initial($userid, $courseid){
        global $DB;
        $record = $DB->get_record_sql('SELECT totalmonths, otjhours, employerorstore, coach, managerormentor, startdate, hoursperweek, annuallw, planfilename FROM {off_the_job_setup} WHERE userid = ? and courseid = ?',[$userid, $courseid]);
        return $record;
    }

    //Get some traning plan data : admin page
    public function admin_plan_data($userid ,$courseid){
        global $DB;
        $record = $DB->get_record_sql('SELECT employer, name, startdate, plannedendd, otjh, epao, fundsource, lengthoprog FROM {off_the_job_plans} WHERE userid = ? and courseid = ?',[$userid, $courseid]);
        return $record;
    }

    //Get setup id
    public function setupid($userid, $courseid){
        global $DB;
        $record = $DB->get_record_sql('SELECT id FROM {off_the_job_setup} WHERE userid = ? AND courseid = ?',[$userid, $courseid]);
        return $record->id;
    }

    //delete learner signature : admin page
    public function admin_del_learn_sign($userid, $courseid){
        global $DB;
        $record = new stdClass();
        $record->id = $this->setupid($userid, $courseid);
        $record->learnersign = null;
        $DB->update_record('off_the_job_setup', $record, false);
        \local_offthejob\event\updated_learner_signature_admin::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
    }

    //delete nta signature : admin page
    public function admin_del_nta_sign($userid, $courseid){
        global $DB;
        $record = new stdClass();
        $record->id = $this->setupid($userid, $courseid);
        $record->ntasign = null;
        $DB->update_record('off_the_job_setup', $record, false);
        \local_offthejob\event\updated_coach_signature_admin::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
    }

    //delete Training Plan : admin page
    public function admin_del_tplan($userid, $courseid){
        global $DB;
        $planid = $this->planid($userid, $courseid);
        $DB->delete_records('off_the_job_plans_fs', [$DB->sql_compare_text('plansid') => $planid]);
        $DB->delete_records('off_the_job_plans_log', [$DB->sql_compare_text('plansid') => $planid]);
        $DB->delete_records('off_the_job_plans_modules', [$DB->sql_compare_text('plansid') => $planid]);
        $DB->delete_records('off_the_job_plans_pr', [$DB->sql_compare_text('plansid') => $planid]);
        $DB->delete_records('off_the_job_plans', [$DB->sql_compare_text('id') => $planid]);
        \local_offthejob\event\deleted_plan_admin::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
        return;
    }

    //Delete initial setup : admin page
    public function admin_del_initial($userid, $courseid){
        global $DB;
        $setupid = $this->setupid($userid, $courseid);
        $DB->delete_records('off_the_job_setup', [$DB->sql_compare_text('id') => $setupid]);
        \local_offthejob\event\deleted_setup_admin::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
        return;
    }

    //Used to update initial setup : admin page
    public function admin_update_setup($userid, $courseid, $array){
        global $DB;
        $setupid = $this->setupid($userid, $courseid);
        $record = new stdClass();
        $record->id = $setupid;
        $record->totalmonths = $array[0][0];
        $record->otjhours = $array[1][0];
        $record->employerorstore = $array[2][0];
        $record->coach = $array[3][0];
        $record->managerormentor = $array[4][0];
        $record->startdate = $array[5][0];
        $record->hoursperweek = $array[6][0];
        $record->anuallw = $array[7][0];
        $record->planfilename = $array[8][0];
        $DB->update_record('off_the_job_setup', $record, false);
        return;
    }

    //Update training plan : admin page
    public function admin_update_plan($userid, $courseid, $leftarray, $progarray, $fsarray, $modarray, $logsarray){
        global $DB;
        $planid = $this->planid($userid, $courseid);
        $record = new stdClass();
        $record->id = $planid;
        $record->name = $leftarray[0];
        $record->employer = $leftarray[1];
        $record->startdate = $leftarray[2];
        $record->plannedendd = $leftarray[3];
        $record->lengthoprog = $leftarray[4];
        $record->otjh = $leftarray[5];
        $record->epao = $leftarray[6];
        $record->fundsource = $leftarray[7];
        $record->bskbrm = $leftarray[8];
        $record->bskre = $leftarray[9];
        $record->learnstyle = $leftarray[10];
        $record->sslearnr = $leftarray[11];
        $record->ssemployr = $leftarray[12];
        $record->apprenhpw = $leftarray[13];
        $record->weekop = $leftarray[14];
        $record->annuall = $leftarray[15];
        $record->pdhours = $leftarray[16];
        $record->areaostren = $leftarray[17];
        $record->longtgoal = $leftarray[18];
        $record->shorttgoal = $leftarray[19];
        $record->iag = $leftarray[20];
        $record->recopl = $leftarray[21];
        $record->addsa = $leftarray[22];
        $DB->update_record('off_the_job_plans', $record, false);
        //input/update progress values
        foreach($progarray as $progarr){
            $exists = $DB->record_exists('off_the_job_plans_pr', [$DB->sql_compare_text('plansid') => $planid, $DB->sql_compare_text('prpos') => $progarr[3]]);
            $probject = new stdClass();
            $probject->prpr = $progarr[1];
            $probject->prar = $progarr[2];
            if($exists == true){
                $probject->id = $DB->get_record_sql('SELECT id FROM {off_the_job_plans_pr} WHERE plansid = ? AND prpos = ?',[$planid, $progarr[3]])->id;
                $probject->prtor = $progarr[0];
                $DB->update_record('off_the_job_plans_pr', $probject, false);
            } elseif($exists == false){
                $probject->plansid = $planid;
                $probject->prpos = $progarr[3];
                $probject->prtor = $progarr[0];
                $DB->insert_record('off_the_job_plans_pr', $probject, false);
            }
        }
        //update functional skills values
        foreach($fsarray as $fsarr){
            $fsobject = new stdClass();
            $fsobject->id = $DB->get_record_sql('SELECT id FROM {off_the_job_plans_fs} WHERE plansid = ? and fspos = ?', [$planid, $fsarr[9]])->id;
            $fsobject->fspos = $fsarr[9];
            $fsobject->fsname = $fsarr[0];
            $fsobject->fslevel = $fsarr[1];
            $fsobject->fsmod = $fsarr[2];
            $fsobject->fssd = $fsarr[3];
            $fsobject->fsped = $fsarr[4];
            $fsobject->fsaed = $fsarr[5];
            $fsobject->fsusd = $fsarr[6];
            $fsobject->fsuped = $fsarr[7];
            $fsobject->fsaead = $fsarr[8];
            $DB->update_record('off_the_job_plans_fs', $fsobject, false);
        }
        //input/update values for changes log
        foreach($logsarray as $logsarr){
            $exists = $DB->record_exists('off_the_job_plans_log', [$DB->sql_compare_text('plansid') => $planid, $DB->sql_compare_text('dateofc') => $logsarr[0]]);
            $logobject = new stdClass();
            $logobject->dateofc = $logsarr[0];
            $logobject->log = $logsarr[1];
            if($exists == true){
                $logobject->id = $DB->get_record_sql('SELECT id FROM {off_the_job_plans_log} WHERE plansid = ? AND dateofc = ?',[$planid, $logsarr[0]])->id;
                $DB->update_record('off_the_job_plans_log', $logobject, false);
            } elseif($exists == false){
                $logobject->plansid = $planid;
                $DB->insert_record('off_the_job_plans_log', $logobject, false);
            }
        }
        //update module values
        foreach($modarray as $modarr){
            $modobject = new stdClass();
            $modobject->id = $DB->get_record_sql('SELECT id FROM {off_the_job_plans_modules} WHERE plansid = ? AND modpos = ?', [$planid, $modarr[10]])->id;
            $modobject->modpos = $modarr[10];
            $modobject->modname = $modarr[0];
            $modobject->modpsd = $modarr[1];
            $modobject->modrsd = $modarr[2];
            $modobject->modped = $modarr[3];
            $modobject->modred = $modarr[4];
            $modobject->modw = $modarr[5];
            $modobject->modotjh = $modarr[6];
            $modobject->modmod = $modarr[7];
            $modobject->modotjt = $modarr[8];
            $modobject->modaotjhc = $modarr[9];
            $DB->update_record('off_the_job_plans_modules', $modobject, false);
        }
    }

    //Check if learner has create a signature : admin page
    public function admin_learn_sign_exists($userid, $courseid){
        global $DB;
        if(!$DB->record_exists('off_the_job_setup',[$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $courseid])){
            return false;
        } else {
            if($DB->get_record_sql('SELECT learnersign FROM {off_the_job_setup} WHERE userid = ? and courseid = ?',[$userid, $courseid])->learnersign == NULL){
                return false;
            }
            return true;
        }
    }

    //Check if coach has created a signature 
    public function nta_sign_exists($userid, $courseid){
        global $DB;
        if(!$DB->record_exists('off_the_job_setup', [$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $courseid])){
            return false;
        } elseif($DB->get_record_sql('SELECT ntasign FROM {off_the_job_setup} WHERE userid = ? and courseid = ?',[$userid, $courseid])->ntasign == null){
            return false;
        }
        return true;
    }

    //Check if a draft exists : admin page
    public function admin_draft_exists($userid, $courseid){
        global $DB;
        $docsid = $this->docsid($userid, $courseid);
        if(!$DB->record_exists('off_the_job_docs_draft', [$DB->sql_compare_text('docsid') => $docsid])){
            return false;
        }
        return true;
    }

    //User for when a draft exists return id : admin page
    public function admin_draft_id($userid, $courseid){
        global $DB;
        $docsid = $this->docsid($userid, $courseid);
        $record = $DB->get_record_sql('SELECT id FROM {off_the_job_docs_draft} WHERE docsid = ?',[$docsid]);
        return $record->id;
    }

    //check if mar document exists : admin page
    public function admin_docs_exists($userid, $courseid){
        global $DB;
        $docsid = $this->docsid($userid, $courseid);
        if(!$DB->record_exists('off_the_job_docs_info', [$DB->sql_compare_text('docsid') => $docsid])){
            return false;
        }
        return true;
    }

    //get list of mar documents : admin page
    public function admin_docs_ids($userid, $courseid){
        global $DB;
        $docsid = $this->docsid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT id, reviewdate FROM {off_the_job_docs_info} WHERE docsid = ?',[$docsid]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->id, date('Y-m-d',$record->reviewdate)]);
        }
        return $array;
    }

    //delete document : admin page
    public function admin_del_doc($id){
        global $DB;
        if($DB->record_exists('off_the_job_docs_info', [$DB->sql_compare_text('id') => $id])){
            $details = $DB->get_record_sql('SELECT userid, courseid FROM {off_the_job_docs} WHERE id = ?',[$DB->get_record_sql('SELECT docsid FROM {off_the_job_docs_info} WHERE id = ?',[$id])->docsid]);
            $DB->delete_records('off_the_job_docs_info', [$DB->sql_compare_text('id') => $id]);
            \local_offthejob\event\deleted_mar_admin::create(array('context' => \context_course::instance($details->courseid), 'relateduserid' => $details->userid, 'courseid' => $details->courseid))->trigger();
        }
        return;
    }

    //update document : admin page
    public function admin_update_doc($id, $array, $userid, $courseid){
        global $DB;
        $docsid = $this->docsid($userid, $courseid);
        $record = $this->admin_array_to_record($array);
        $record->id = $id;
        $record->docsid = $docsid;
        $DB->update_record('off_the_job_docs_info', $record, false);
        \local_offthejob\event\updated_mar_admin::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
    }

    //delete draft : admin page
    public function admin_del_draft($id){
        global $DB;
        if($DB->record_exists('off_the_job_docs_draft', [$DB->sql_compare_text('id') => $id])){
            $details = $DB->get_record_sql('SELECT userid, courseid FROM {off_the_job_docs} WHERE id = ?',[$DB->get_record_sql('SELECT docsid FROM {off_the_job_docs_draft} WHERE id = ?',[$id])->docsid]);
            $DB->delete_records('off_the_job_docs_draft', [$DB->sql_compare_text('id') => $id]);
            \local_offthejob\event\deleted_mar_draft_admin::create(array('context' => \context_course::instance($details->courseid), 'relateduserid' => $details->userid, 'courseid' => $details->courseid))->trigger();
        }
        return;
    }

    //create document from draft : admin page
    public function admin_draft_to_doc($array, $userid, $courseid){
        global $DB;
        $record =  $this->admin_array_to_record($array);
        $docsid = $this->docsid($userid, $courseid);
        $record->docsid = $docsid;
        $DB->insert_record('off_the_job_docs_info', $record);
        \local_offthejob\event\created_mar_admin::create(array('context' => \context_course::instance($courseid), 'relateduserid' => $userid, 'courseid' => $courseid))->trigger();
        return;
    }

    //admin record to array : admin page
    public function admin_array_to_record($array){
        $record = new stdClass();
        $record->apprentice = $array[0];
        $record->reviewdate = $array[1];
        $record->standard = $array[2];
        $record->employerandstore = $array[3];
        $record->coach = $array[4];
        $record->managerormentor = $array[5];
        $record->progress = $array[6];
        $record->hours = $array[7];
        $record->recap = $array[8];
        $record->impact = $array[9];
        $record->details = $array[10];
        $record->detailsksb = $array[11];
        $record->detailimpact = $array[12];
        $record->todaymath = $array[13];
        $record->nextmath = $array[14];
        $record->todayict = $array[15];
        $record->todayeng = $array[16];
        $record->nexteng = $array[17];
        $record->nextict = $array[18];
        $record->activity = $array[19];
        $record->activityksb = $array[20];
        $record->safeguarding = $array[21];
        $record->agreedaction = $array[22];
        $record->employercomment = $array[23];
        $record->apprenticecomment = $array[24];
        $record->ntasigndate = $array[25];
        $record->learnsigndate = $array[26];
        $record->expectprogress = $array[27];
        $record->expecthours = $array[28];
        $record->alnsupport = $array[29];
        $record->progresscom = $array[30];
        $record->otjhcom = $array[31];
        return $record;
    }

    //update hours record : admin page
    public function admin_update_hour($array, $id, $userid, $courseid){
        global $DB;
        $record = new stdClass();
        $record->id = $id;
        $hoursid = $this->hoursid($userid, $courseid);
        $record->hoursid = $hoursid;
        $record->date = $array[0];
        $record->activity = $array[1];
        $record->whatlink = $array[2];
        $record->impact = $array[3];
        $record->duration = $array[4];
        $record->initial = $array[5];
        $DB->update_record('off_the_job_hours_info', $record, false);
    }

    //Get all learners, courseid and userid : admin page
    public function admin_learners(){
        global $DB;
        $apprenticeship = 'Apprenticeships';
        //get enrolments for all courses
        $userenrolments = $DB->get_records('user_enrolments');
        $enrols = $DB->get_records('enrol');
        $courseids = [];
        foreach($userenrolments as $userenrol){
            foreach($enrols as $enrol){
                if($enrol->id == $userenrol->enrolid && $enrol->status !== 1){
                    array_push($courseids, [$enrol->courseid, $userenrol->userid]);
                }
            }
        }
        //get students of the course
        $coursetemp = [];
        $contexts = $DB->get_records('context');
        $roleassignments = $DB->get_records_sql('SELECT * FROM {role_assignments} WHERE roleid = ?',[5]);
        foreach($contexts as $context){
            foreach($roleassignments as $roleassign){
                if($roleassign->contextid == $context->id){
                    array_push($coursetemp, [$context->instanceid, $roleassign->userid]);
                }
            }
        }
        $coursefinal = [];
        foreach($coursetemp as $ctemp){
            foreach($courseids as $cids){
                if($ctemp == $cids){
                    array_push($coursefinal, $cids);
                }
            }
        }
        //add category id to array
        $coursenames = [];
        $courses = $DB->get_records('course');
        foreach($coursefinal as $courfinal){
            foreach($courses as $cou){
                if($courfinal[0] == $cou->id){
                    array_push($coursenames, [$courfinal[0], $courfinal[1], $cou->category]);
                }
            }
        }
        //filter out none apprenticeship
        $apprenticeid = $DB->get_record_sql('SELECT id FROM {course_categories} WHERE name = ?',[$apprenticeship]);
        $apprenticearray = [];
        $coursefinal = [];
        foreach($coursenames as $coursename){
            if($coursename[2] == $apprenticeid->id){
                array_push($apprenticearray, [$coursename[0], $coursename[1]]);
            }
        }
        return $apprenticearray;
    }

    //Get all apprenticeship courses : unused atm
    public function admin_apprentice_courses(){
        global $DB;
        $apprenticeid = $DB->get_record_sql('SELECT id FROM {course_categories} WHERE name = ?',['Apprenticeships']);
        $courses = $DB->get_records('course');
        $array = [];
        foreach($courses as $course){
            if($course->category == $apprenticeid->id){
                array_push($array, [$course->id, $course->fullname]);
            }
        }
        return $array;
    }

    //Get all learners without initial setup : admin page
    public function admin_setup_incomplete(){
        global $DB;
        $apprentices = $this->admin_learners();
        $array = [];
        foreach($apprentices as $apprentice){
            if(!$DB->record_exists('off_the_job_setup', [$DB->sql_compare_text('userid') => $apprentice[1], $DB->sql_compare_text('courseid') => $apprentice[0]])){
                $username = $DB->get_record_sql('SELECT firstname, lastname FROM {user} WHERE id = ?',[$apprentice[1]]);
                $username = $username->firstname . ' '. $username->lastname;
                $coursename = $DB->get_record_sql('SELECT fullname FROM {course} WHERE id = ?',[$apprentice[0]]);
                $coursename = $coursename->fullname;
                array_push($array, [$username, $coursename]);
            }
        }
        asort($array);
        return $array;
    }

    //Get all Learners with completed setups and data related to them : admin page
    public function admin_setup_complete(){
        global $DB;
        $apprentices = $this->admin_learners();
        $array = [];
        $int = 0;
        foreach($apprentices as $apprentice){
            if($DB->record_exists('off_the_job_setup', [$DB->sql_compare_text('userid') => $apprentice[1], $DB->sql_compare_text('courseid') => $apprentice[0]])){
                $record = $DB->get_record_sql('SELECT firstname, lastname FROM {user} WHERE id = ?',[$apprentice[1]]);
                $array[$int][0] = $record->firstname . ' ' . $record->lastname;
                $array[$int][1] = $DB->get_record_sql('SELECT fullname FROM {course} WHERE id = ?',[$apprentice[0]])->fullname;
                if($DB->record_exists('off_the_job_plans', [$DB->sql_compare_text('userid') => $apprentice[1], $DB->sql_compare_text('courseid') => $apprentice[0]])){
                    $array[$int][2] = 'Yes';
                    $array[$int][3] = 'green';
                } else {
                    $array[$int][2] = 'No';
                    $array[$int][3] = 'red';
                }
                if($DB->record_exists('off_the_job_docs', [$DB->sql_compare_text('userid') => $apprentice[1], $DB->sql_compare_text('courseid') => $apprentice[0]])){
                    $array[$int][4] = 'Yes';
                    $array[$int][5] = 'green';
                } else {
                    $array[$int][4] = 'No';
                    $array[$int][5] = 'red';
                }
                if($DB->record_exists('off_the_job_hours', [$DB->sql_compare_text('userid') => $apprentice[1], $DB->sql_compare_text('courseid') => $apprentice[0]])){
                    $array[$int][6] = 'Yes';
                    $array[$int][7] = 'green';
                } else {
                    $array[$int][6] = 'No';
                    $array[$int][7] = 'red';
                }
                $int++;
            }
        }
        asort($array);
        return $array;
    }

    //delete drafts employer comment pdf : admin page
    public function admin_get_draft_ecom($id){
        global $DB;
        $pdf = $DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_draft} WHERE id = ?',[$id])->employercomment;
        if(!empty($pdf)){
            $pdf = explode('/', $pdf);
            $pdf = end($pdf);
            return $pdf;
        }
    }

    //check if employer comment is used elsewhere draft to info : admin page
    public function admin_get_draft_ecom_used($id){
        global $DB;
        $record = $DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_draft} WHERE id = ?',[$id])->employercomment;
        if(!empty($record)){
            if($DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_info} WHERE employercomment = ?',[$record])->employercomment === $record){
                return true;
            } else{
                return false;
            }
        } else {
            return false;
        }
    }

    //check if employer comment is used elsewhere info to draft : admin page
    public function admin_get_docs_ecom_used($id){
        global $DB;
        $record = $DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_info} WHERE id = ?',[$id])->employercomment;
        if(!empty($record)){
            if($DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_draft} WHERE employercomment = ?',[$record])->employercomment === $record){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //check for nta signature : teacher page
    public function teach_check_signature($userid, $courseid){
        global $DB;
        if(!$DB->record_exists('off_the_job_setup', [$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $courseid])){
            return true;
        } else {
            $record = $DB->get_record_sql('SELECT ntasign FROM {off_the_job_setup} WHERE userid = ? and courseid = ?', [$userid, $courseid])->ntasign;
            if($record !== null){
                return true;
            } else {
                return false;
            }
        }
    }

    //get all monthly activity document dates : teacher page
    public function plan_ar_dates($userid, $courseid){
        global $DB;
        $id = $this->docsid($userid, $courseid);
        $records = $DB->get_records_sql('SELECT reviewdate FROM {off_the_job_docs_info} WHERE docsid = ?',[$id]);
        $list = [];
        foreach($records as $record){
            array_push($list, [date('d-m-Y',$record->reviewdate)]);
        }
        return $list;
    }

    //get employer comment pdf : teacher page
    public function get_employer_comment_info($id){
        global $DB;
        $record = $DB->get_record_sql('SELECT employercomment FROM {off_the_job_docs_info} WHERE id = ?',[$id]);
        if($record->employercomment !== null){
            return $record->employercomment;
        } else {
            return 'Empty.pdf';
        }
    }

    //get employer comment pdf : learner page
    public function get_employer_comment_info_learn($id, $courseid){
        global $DB;
        global $USER;
        $userid = $USER->id;
        $record = $DB->get_record_sql('SELECT docsid, employercomment FROM {off_the_job_docs_info} WHERE id = ?',[$id]);
        if($DB->record_exists('off_the_job_docs', [$DB->sql_compare_text('id') => $record->docsid, $DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $courseid])){
            return $record->employercomment;
        } else {
            return 'Empty.pdf';
        }
    }

    //check if the teacher is the teacher for a user and coursae : teacher page
    public function check_teach_setup($userid, $courseid){
        global $DB;
        global $USER;
        $teachid = $USER->id;
        if($DB->record_exists('off_the_job_setup', [$DB->sql_compare_text('userid') => $userid, $DB->sql_compare_text('courseid') => $courseid, $DB->sql_compare_text('teachid') => $teachid])){
            return true;
        } else {
            return false;
        }
    }

    //Check for users who are behind target : admin page
    public function behind_target(){
        global $DB;
        $records = $DB->get_records('off_the_job_setup');
        $users = [];
        foreach($records as $record){
            $percent = $this->get_percent_hours($record->userid, $record->courseid);
            $expect = $this->get_percent_expect($record->userid, $record->courseid);
            $comp = $this->assign_comp($record->userid, $record->courseid);
            $username = $this->get_username($record->userid);
            $coursename = $this->get_coursename($record->courseid);
            $colour1 = 'red';
            $colour2 = 'red';
            $type1 = 'Behind Target';
            $type2 = 'Behind Target';
            if($percent >= $expect){
                if($comp[0] >= $comp[1]){
                    $colour1 = 'green';
                    $colour2 = 'green';
                    $type1 = 'On Target';
                    $type2 = 'On Target';
                } else{
                    $colour1 = 'green';
                    $colour2 = 'red';
                    $type1 = 'On Target';
                    $type2 = 'Behind Target';
                }
            } else{
                if($comp[0] >= $comp[1]){
                    $colour1 = 'red';
                    $colour2 = 'green';
                    $type1 = 'Behind Target';
                    $type2 = 'On Target';
                } else{
                    $colour1 = 'red';
                    $colour2 = 'red';
                    $type1 = 'Behind Target';
                    $type2 = 'Behind Target';
                }
            }
            array_push($users, [$username->username, $coursename->fullname, $colour1, $type1, $colour2, $type2]);
        }
        asort($users);
        return $users;
    }

    //Get Learner Documents without signatures : admin page
    public function doc_not_signed(){
        global $DB;
        $records = $DB->get_records('off_the_job_docs_info');
        $signatures = [];
        foreach($records as $record){
            $temp = [];
            if($record->ntasigndate !== null){
                $temp[0][0] = 'green';
                $temp[0][1] = 'Signed';
            } else {
                $temp[0][0] = 'red';
                $temp[0][1] = 'Not Signed';
            }
            if($record->learnsigndate !== null){
                $temp[1][0] = 'green';
                $temp[1][1] = 'Signed';
            } else {
                $temp[1][0] = 'red';
                $temp[1][1] = 'Not Signed';
            }
            if($temp[1][0] === 'red' || $temp[0][0] === 'red'){
                $ids = $DB->get_record_sql('SELECT userid, courseid FROM {off_the_job_docs} WHERE id = ?',[$record->docsid]);
                $username = $this->get_username($ids->userid)->username;
                $coursename = $this->get_coursename($ids->courseid)->fullname;
                array_push($signatures, [$username, $coursename, date('d-m-y',$record->reviewdate), $temp[0][0], $temp[0][1], $temp[1][0], $temp[1][1]]);
            }
        }
        asort($signatures);
        return $signatures;
    }

    //Get individual progress for all users : admin page
    public function get_progress_all(){
        global $DB;
        $records = $DB->get_records('off_the_job_setup');
        $users = [];
        foreach($records as $record){
            $percent = $this->get_percent_hours($record->userid, $record->courseid);
            $expect = $this->get_percent_expect($record->userid, $record->courseid);
            $comp = $this->assign_comp($record->userid, $record->courseid);
            $username = $this->get_username($record->userid);
            $coursename = $this->get_coursename($record->courseid);
            array_push($users, [$username->username, $coursename->fullname, $percent, $expect, $comp[0], $comp[1], $record->userid, $record->courseid]);
        }
        asort($users);
        return $users;
    }

    //Get setup completion for all users : admin page
    public function admin_setup_completion(){
        global $DB;
        $apprentices = $this->admin_learners();
        //[incomplete, complete]
        $array = [0, 0];
        foreach($apprentices as $apprentice){
            if(!$DB->record_exists('off_the_job_setup', [$DB->sql_compare_text('userid') => $apprentice[1], $DB->sql_compare_text('courseid') => $apprentice[0]])){
                $array[0] += 1;
            } else {
                $array[1] += 1;
            }
        }
        return $array;
    }

    //Plan used and unused with an existing setup : admin page
    public function admin_plan_used(){
        global $DB;
        $records = $DB->get_records('off_the_job_setup');
        //[unused, used]
        $array = [0, 0];
        foreach($records as $record){
            if(!$DB->record_exists('off_the_job_plans', [$DB->sql_compare_text('userid') => $record->userid, $DB->sql_compare_text('courseid') => $record->courseid])){
                $array[0] += 1;
            } else {
                $array[1] += 1;
            }
        }
        return $array;
    }

    //Plan used and unused with exisiting setup, return name and course : admin page
    public function admin_plan_used_who(){
        global $DB;
        $records = $DB->get_records('off_the_job_setup');
        $array = [];
        foreach($records as $record){
            if(!$DB->record_exists('off_the_job_plans', [$DB->sql_compare_text('userid') => $record->userid, $DB->sql_compare_text('courseid') => $record->courseid])){
                array_push($array, [
                    $this->get_username($record->userid)->username,
                    $this->get_coursename($record->courseid)->fullname
                ]);
            }
        }
        asort($array);
        return $array;
    }
}