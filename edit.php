<?php

require('../../config.php');
require_once('edit_form.php');
global $SITE;
$courseid   = required_param('courseid', PARAM_INT);
$instanceid = optional_param('id', 0, PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('enrol/eventenrollment:config', $context);

$PAGE->set_url('/enrol/eventenrollment/edit.php', array('courseid' => $course->id, 'id' => $instanceid));
$PAGE->set_pagelayout('admin');

$return = new moodle_url('/enrol/instances.php', array('id' => $course->id));
if (!enrol_is_enabled('eventenrollment')) {
    redirect($return);
}

$plugin = enrol_get_plugin('eventenrollment');

if ($instanceid) {
    $instance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'eventenrollment',
                'id' => $instanceid), '*', MUST_EXIST);
} else {
    require_capability('moodle/course:enrolconfig', $context);
    // No instance yet, we have to add new instance.
    navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id' => $course->id)));
    $instance = new stdClass();
    $instance->id       = null;
    $instance->courseid = $course->id;
}
$email  = $DB->get_field('enrol_eventenrollment', 'email', array('instanceid'=>$instanceid));
$instance->email = $email;
$mform = new enrol_eventenrollment_edit_form(null, array($instance, $plugin, $context));

if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {

    if ($instance->id) {
        $instance->status         = $data->status;
        $instance->name           = $data->name;
        $instance->roleid         = $data->roleid;
        $instance->enrolperiod    = $data->enrolperiod;
        $instance->enrolstartdate = $data->enrolstartdate;
        $instance->enrolenddate   = $data->enrolenddate;
        $instance->timemodified   = time();
        $DB->update_record('enrol', $instance);
    } else {
        $fields = array('status' => $data->status, 'name' => $data->name, 'roleid' => $data->roleid,
                        'enrolperiod' => $data->enrolperiod, 'enrolstartdate' => $data->enrolstartdate,
                        'enrolenddate' => $data->enrolenddate);
        $plugin->add_instance($course, $fields);
    }

    redirect($return);
}

$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'enrol_eventenrollment'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'enrol_eventenrollment'));
$mform->display();
echo $OUTPUT->footer();
