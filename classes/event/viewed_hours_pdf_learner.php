<?php
/**
 * @package     local_offthejob
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

namespace local_offthejob\event;
use core\event\base;
defined('MOODLE_INTERNAL') || die();

class viewed_hours_pdf_learner extends base {
    protected function init(){
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }
    public static function get_name(){
        return "Learner off the job hours pdf viewed";
    }
    public function get_description(){
        return "The user with id '".$this->userid."' viewed off the job hours pdf for the user with id '".$this->relateduserid."' and for the course with id '".$this->courseid."'";
    }
    public function get_url(){
        return new \moodle_url('/local/offthejob/classes/pdf/hourspdf_learn.php?courseid='.$this->courseid);
    }
    public function get_id(){
        return $this->objectid;
    }
}