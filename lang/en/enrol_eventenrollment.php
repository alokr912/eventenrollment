<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

$string['enrolname'] = 'Event Enrollment Plugin';
$string['pluginname'] = 'Event Enrollment Plugin';
$string['pluginname_desc'] = 'Enrols the user in the course when he event enrollment in Moodle';

$string['assignrole'] = 'Assign role';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users';
$string['enrolenddate'] = 'End date';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolperiod'] = 'Enrolment period';
$string['enrolperiod_desc'] = 'Default length of the enrolment period (in seconds).';
$string['enrolstartdate'] = 'Start date';
$string['mailadmins'] = 'Notify admin';
$string['mailstudents'] = 'Notify students';
$string['mailteachers'] = 'Notify teachers';
$string['nocost'] = 'There is no cost associated with enrolling in this course!';
$string['eventenrollment:config'] = 'Configure eventenrollment enrol instances';
$string['eventenrollment:manage'] = 'Manage enrolled users';
$string['eventenrollment:unenrol'] = 'Unenrol users from course';
$string['eventenrollment:unenrolself'] = 'Unenrol self from the course';
$string['status'] = 'Allow eventenrollment enrolments';
$string['status_desc'] = 'Enrol users in the course on eventenrollment';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['email'] = 'Email';
$string['emailsignupconfirmation'] = 'Hi Dear,

A Signup page was requested for you to enroll in {$a->coursename}.

To confirm this request, Please go to Signup page, please
go to the following web address:

{$a->link}
(This link is valid for {$a->resetminutes} minutes from the time this mail was requested)

If you need help, please contact the site administrator,
{$a->admin}';
$string['emailsubject'] = '{$a}: Signup Request';
$string['inviteusers'] = 'Invite users';
$string['emailssent'] = 'Email(s) have been sent.';
$string['courseemailenrollment'] = 'Hi Dear,
        
You have been enrolled to course {$a->coursename}.
        
Thanks,
{$a->admin}';
       
        
$string['email_help'] = 'Please enter email separated by commas to invite.'; 