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

class deleted_mar_draft_admin extends base {
    protected function init(){
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
    public static function get_name(){
        return "Monthly activity record draft deleted by admin";
    }
    public function get_description(){
        return "The user with id '".$this->userid."' deleted a monthly activity record for the user with id '".$this->relateduserid."' and for the course with id '".$this->courseid."'";
    }
    public function get_url(){
        return new \moodle_url('/local/offthejob/otj_doc_admin.php?userid='.$this->relateduserid.'&courseid='.$this->courseid);
    }
    public function get_id(){
        return $this->objectid;
    }
}