<?php
/**
* Adds Admin settings for the plugin
* @package     local_offthejob
* @author      Robert
* @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

if($hassiteconfig){
    $ADMIN->add('localplugins', new admin_externalpage('local_offthejob', 'Manage Off The Job',
        $CFG->wwwroot . '/local/offthejob/admin.php'));
}