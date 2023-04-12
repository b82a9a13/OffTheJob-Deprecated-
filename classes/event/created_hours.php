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

class created_hours extends base {
    protected function init(){
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }
    public static function get_name(){
        return "Hours record created";
    }
    public function get_description(){
        return "The user with id '".$this->userid."' created a hours record for the user with id '".$this->relateduserid."' and for the course with id '".$this->courseid."'";
    }
    public function get_url(){
        return new \moodle_url('/local/offthejob/otj_hours.php?userid='.$this->relateduserid.'&courseid='.$this->courseid);
    }
    public function get_id(){
        return $this->objectid;
    }
}