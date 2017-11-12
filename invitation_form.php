<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}


require_once ('locallib.php');
require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/lib/enrollib.php');

class invitation_form extends moodleform {

    /**
     * The form definition
     */
    function definition() {
        global $CFG, $USER, $OUTPUT, $PAGE;
        $mform = & $this->_form;

        // Add some hidden fields
        $courseid = $this->_customdata['courseid'];
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);


        $mform->addElement('textarea', 'email', get_string('email', 'enrol_eventenrollment'), 'wrap="virtual" rows="8" cols="80"');
        $mform->addRule('email', get_string('required'), 'required');
        $mform->addHelpButton('email', 'email', 'enrol_eventenrollment');
        $mform->setType('email', PARAM_TEXT);
        $this->add_action_buttons(false, get_string('inviteusers', 'enrol_eventenrollment'));
    }

    function validation($data, $files) {
        parent::validation($data, $files);
        $errors = parent::validation($data, $files);
        if (empty($data['email'])) {
            $errors['eamil'] = 'Please enter email into text area to invite';
        } else {
            $emails = str_getcsv($data['email'], ',');
            foreach ($emails as $email) {
                if (!empty($email) && validate_email($email)) {
                    
                } else {
                    $errors['email'] = 'Invalid email';
                }
            }
        }
        return $errors;
    }

}
