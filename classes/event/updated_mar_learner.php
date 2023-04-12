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

class updated_mar_learner extends base {
    protected function init(){
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }
    public static function get_name(){
        return "Monthly activity record updated by learner";
    }
    public function get_description(){
        return "The user with id '".$this->userid."' updated a monthly activity record for the user with id '".$this->relateduserid."' and for the course with id '".$this->courseid."'";
    }
    public function get_url(){
        return new \moodle_url('/local/offthejob/otj_doc_learn.php?courseid='.$this->courseid.'&type=update&form=true&id='.$this->other);
    }
    public function get_id(){
        return $this->objectid;
    }
}