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

class updated_coach_signature_admin extends base {
    protected function init(){
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'off_the_job_setup';
    }
    public static function get_name(){
        return "Admin updated coach signature";
    }
    public function get_description(){
        return "The user with id '".$this->userid."' updated the coach signature for the user with id '".$this->relateduserid."' and for the course with id '".$this->courseid."'";
    }
    public function get_url(){
        return new \moodle_url('/local/offthejob/admin.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}