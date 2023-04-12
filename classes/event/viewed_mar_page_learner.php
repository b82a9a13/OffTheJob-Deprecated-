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

class viewed_mar_page_learner extends base {
    protected function init(){
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }
    public static function get_name(){
        return "Learner monthly activity report page viewed";
    }
    public function get_description(){
        return "The user with id '".$this->userid."' viewed monthly activity report page for the user with id '".$this->relateduserid."' and for the course with id '".$this->courseid."'";
    }
    public function get_url(){
        return new \moodle_url('/local/offthejob/otj_doc_learn.php?courseid='.$this->courseid);
    }
    public function get_id(){
        return $this->objectid;
    }
}