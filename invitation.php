<?php

require('../../config.php');
require_once('locallib.php');
require_once('invitation_form.php');
require_once("$CFG->dirroot/enrol/locallib.php");
require_login();

$courseid = required_param('courseid', PARAM_INT);
$id = required_param('id', PARAM_INT);
$courseurl = new moodle_url('/course/view.php', array('id' => $id, 'courseid' => $courseid));
$pageurl = new moodle_url('/enrol/eventenrollment/invitation.php', array('id' => $id, 'courseid' => $courseid));
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$fullname = $course->fullname;
$context = context_course::instance($courseid);

if (!has_capability('enrol/eventenrollment:config', $context)) {
    throw new moodle_exception('nopermission');
}

$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('course');
$PAGE->set_course($course);
$PAGE->set_heading(get_string('inviteusers', 'enrol_eventenrollment'));
$PAGE->set_title(get_string('inviteusers', 'enrol_eventenrollment'));
$PAGE->navbar->add(get_string('inviteusers', 'enrol_eventenrollment'));

$enrol = new enrol_eventenrollment_handler();
$instance = $enrol->get_event_instance($courseid, true);
//$invitationleft = $invitationmanager->leftinvitationfortoday($courseid);
$mform = new invitation_form();
$mform->set_data(array('courseid' => $courseid, 'id' => $id));

$instanceobj = $DB->get_record('enrol', array('courseid' => $courseid, 'enrol' => 'eventenrollment',
    'id' => $instance->id), '*', MUST_EXIST);

$data = $mform->get_data();
$confirmation = '';
if ($data and confirm_sesskey()) {





    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('inviteusers', 'enrol_eventenrollment'), 3, 'main');
    if (send_invite($data)) {
        echo $OUTPUT->notification(get_string('emailssent', 'enrol_eventenrollment'), 'notifysuccess');
    } else {
          echo $OUTPUT->notification(get_string('emailssent', 'enrol_eventenrollment'), 'notifyfailure');
    }

    echo $OUTPUT->continue_button(new moodle_url('/course/view.php', array('id' => $courseid)));
    echo $OUTPUT->footer();
    exit();
} else {
    //OUTPUT form
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('inviteusers', 'enrol_eventenrollment'), 3, 'main');
    $mform->display();
    echo $OUTPUT->footer();
}

