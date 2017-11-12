<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Event handler for eventenrollment enrol plugin.
 */
class enrol_eventenrollment_handler {

    public static function user_created(\core\event\user_created $event) {
        global $CFG, $DB;
        $user = $event->get_record_snapshot('user', $event->objectid);
        $data = $DB->get_record('enrol_eventenrollment', array('email' => $user->email));
        if ($user->email == $data->email) {
            $eventenrollmentinstances = $DB->get_records('enrol', array('enrol' => 'eventenrollment'));
//        foreach ($eventenrollmentinstances as $si) {
//                 $courseid = $si->courseid;
            self::eventenrollment_enrol_user($user->username, $data->courseid, $si->roleid);
            // }
        }
    }

    public static function eventenrollment_enrol_user($username, $courseid, $roleid = 5) {
        global $CFG, $DB, $PAGE;

        require_once("$CFG->dirroot/enrol/locallib.php");

        $conditions = array('username' => $username);
        $user = $DB->get_record('user', $conditions);
        $conditions = array('id' => $courseid);
        $course = $DB->get_record('course', $conditions);

        // First, check if user is already enroled but suspended, so we just need to enable it.
        $conditions = array('courseid' => $courseid, 'enrol' => 'manual');
        $enrol = $DB->get_record('enrol', $conditions);

        $conditions = array('username' => $username);
        $user = $DB->get_record('user', $conditions);

        $conditions = array('enrolid' => $enrol->id, 'userid' => $user->id);
        $ue = $DB->get_record('user_enrolments', $conditions);

        if ($ue) {
            // User already enroled but suspended. Just activate enrolment and return.
            $ue->status = 0; // Active.
            $DB->update_record('user_enrolments', $ue);
            return 1;
        }

        $manager = new course_enrolment_manager($PAGE, $course);
        $instances = $manager->get_enrolment_instances();
        $plugins = $manager->get_enrolment_plugins();

        $today = time();
        $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), date('H', $today), date('i', $today), date('s', $today));

        $timestart = $today;
        $timeend = 0;

        foreach ($instances as $instance) {
            if ($instance->enrol == 'eventenrollment') {
                break;
            }
        }

        $plugin = $plugins['eventenrollment'];

        if ($instance->enrolperiod) {
            $timeend = $timestart + $instance->enrolperiod;
        }
        $plugin->enrol_user($instance, $user->id, $roleid, $timestart, $timeend);
        require_once($CFG->libdir . '/moodlelib.php');
        $sender = get_admin();
        $text = new stdClass();
        $text->admin = generate_email_signoff();
        $text->coursename = $course->fullname;
        $message = get_string('courseemailenrollment', 'enrol_eventenrollment', $text);
        email_to_user($user, $sender, 'Enrollment Notification', $message);
        return 1;
    }

    public function get_event_instance($courseid, $mustexist = false) {
        global $PAGE, $CFG, $DB;
        //find enrolment instance
        $instance = null;
        require_once("$CFG->dirroot/enrol/locallib.php");
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        $manager = new course_enrolment_manager($PAGE, $course);
        foreach ($manager->get_enrolment_instances() as $tempinstance) {
            if ($tempinstance->enrol == 'eventenrollment') {
                if ($instance === null) {
                    $instance = $tempinstance;
                }
            }
        }

        if ($mustexist and empty($instance)) {
            throw new moodle_exception('noinvitationinstanceset', 'enrol_eventenrollment');
        }

        return $instance;
    }

}

function send_invite($data) {
    global $DB, $USER, $CFG, $SITE;

    $course = get_course($data->courseid);
    $enrolinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'eventenrollment',
        'id' => $data->id), '*', MUST_EXIST);


    if (empty($data->email)) {
        print_error('Email cant be blank');
    }

    $emails = str_getcsv($data->email, ',');
    foreach ($emails as $email) {



        $inviteinstance = new stdClass();
        $inviteinstance->expire = 14 * 24 * 60 * 60;
        $inviteinstance->status = 0;
        $inviteinstance->email = $email;
        $inviteinstance->name = $enrolinstance->name;
        $inviteinstance->instanceid = $enrolinstance->id;
        $inviteinstance->userid = $USER->id;
        $inviteinstance->courseid = $course->id;
        $inviteinstance->timeupdated = time();
        $inviteinstance->id = $DB->insert_record('enrol_eventenrollment', $inviteinstance);
        
        $tempuser = new stdClass;
        $tempuser->email = $email;
        $tempuser->id = -1;
        $tempuser->deleted = 0;
        $tempuser->auth = 'manual';
        $tempuser->suspended = 0;
        $tempuser->mailformat = 1;


        $pwresetmins = isset($CFG->pwresettime) ? floor($CFG->pwresettime / MINSECS) : 30;
        $sender = get_admin();
        $text = new stdClass();
        $text->link = $CFG->wwwroot . '/enrol/eventenrollment/confirm.php?inviteid=' . $inviteinstance->id . '&courseid=' . $course->id;
        $text->admin = generate_email_signoff();
        $text->resetminutes = $pwresetmins;
        $text->coursename = $course->fullname;
        $message = get_string('emailsignupconfirmation', 'enrol_eventenrollment', $text);
        $subject = get_string('emailsubject', 'enrol_eventenrollment', format_string($SITE->fullname));

        email_to_user($tempuser, $sender, $subject, $message);
        return true;
    }
}
