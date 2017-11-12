<?php

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/enrol/locallib.php");

class enrol_eventenrollment_plugin extends enrol_plugin {

    public function roles_protected() {
        // Users with role assign cap may tweak the roles later.
        return false;
    }

    public function allow_unenrol(stdClass $instance) {
        return true;
    }

    public function allow_manage(stdClass $instance) {
        return true;
    }

    public function show_enrolme_link(stdClass $instance) {
        return ($instance->status == ENROL_INSTANCE_ENABLED);
    }

    /**
     * Sets up navigation entries.
     *
     * @param object $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'eventenrollment') {
             throw new coding_exception('Invalid enrol instance type!');
        }
        $context = context_course::instance($instance->courseid);

        if (has_capability('enrol/eventenrollment:config', $context)) {
            $managelink = new moodle_url('/enrol/eventenrollment/edit.php', array('courseid' => $instance->courseid,
                        'id' => $instance->id));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'eventenrollment') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();
        if (has_capability('enrol/eventenrollment:config', $context)) {
            $managelink = new moodle_url("/enrol/eventenrollment/invitation.php", array('courseid'=>$instance->courseid,
                        'id' => $instance->id));
            $icons[] = $OUTPUT->action_icon($managelink, new pix_icon('t/enrolusers',
                get_string('enrolusers', 'enrol_manual'), 'core', array('class'=>'iconsmall')));
        }
        if (has_capability('enrol/eventenrollment:config', $context)) {
            $editlink = new moodle_url("/enrol/eventenrollment/edit.php", array('courseid' => $instance->courseid,
                        'id' => $instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('i/edit', get_string('edit'),
                        'core', array('class' => 'icon')));
        }

        return $icons;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        $context = context_course::instance($courseid);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/eventenrollment:config', $context)) {
            return null;
        }

        return new moodle_url('/enrol/eventenrollment/edit.php', array('courseid' => $courseid));
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/eventenrollment:config', $context);
    }

}
