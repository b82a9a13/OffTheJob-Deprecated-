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

class viewed_admin_reports extends base {
    protected function init(){
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
    public static function get_name(){
        return 'Admin reports viewed';
    }
    public function get_description(){
        return "The user with id '".$this->userid."' viewed admin reports.";
    }
    public function get_url(){
        return new \moodle_url('/local/offthejob/admin_reports.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}