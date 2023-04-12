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

class created_mar_draft extends base {
    protected function init(){
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }
    public static function get_name(){
        return "Monthly activity record draft created by coach";
    }
    public function get_description(){
        return "The user with id '".$this->userid."' created a monthly activity record draft for the user with id '".$this->relateduserid."' and for the course with id '".$this->courseid."'";
    }
    public function get_url(){
        return new \moodle_url('/local/offthejob/otj_doc.php?userid='.$this->relateduserid.'&courseid='.$this->courseid.'&form=true');
    }
    public function get_id(){
        return $this->objectid;
    }
}