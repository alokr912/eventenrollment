<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('../../config.php');
include 'locallib.php';
$invite  = required_param('inviteid',PARAM_INT);
$courseid  = required_param('courseid',PARAM_INT);
$data = $DB->get_record('enrol_eventenrollment',array('id'=>$inviteid));
$user = $DB->get_record('user',array('email'=>$email));
if($user->email){
    $enrol = new enrol_eventenrollment_handler();
    $enrol->eventenrollment_enrol_user($user->username,$courseid,5);
    $return = $CFG->httpswwwroot .'/course/view.php?id='.$courseid;
    redirect($return);  
}
$pwresettime = isset($CFG->pwresettime) ? $CFG->pwresettime : 1800;
if ($data->timeupdated < (time() - $pwresettime)) {
    // There is a reset record, but it's expired.
    // Direct the user to the forgot password page to request a password reset.
    $pwresetmins = floor($pwresettime / MINSECS);
    echo $OUTPUT->header();
    notice("Link Expired!","$CFG->httpswwwroot",null);
    die;

}else {
    $return = $CFG->httpswwwroot .'/login/signup.php';
    redirect($return);
}


$PAGE->set_heading('Confirmation Page.');
$PAGE->set_title(get_string('pluginname', 'enrol_eventenrollment'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'enrol_eventenrollment'));
echo $OUTPUT->footer();