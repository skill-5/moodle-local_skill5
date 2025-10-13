<?php
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Skill5 Moodle';
$string['overview'] = 'Overview';
$string['failedcreateltool'] = 'Failed to Create LTI Tool';
$string['connectionfailed'] = 'Connection to Skill5 API Failed';
$string['admin_email'] = 'Skill5 Administrator Email';
$string['admin_email_desc'] = 'Enter the email associated with your Skill5 administrator account.';
$string['entityuserid_from_email'] = 'Skill5 EntityUser ID';
$string['entityuserid_from_email_desc'] = 'This will be automatically fetched after connecting.';
$string['connect_button'] = 'Connect with Skill5';
$string['connection_failed'] = 'Connection to Skill5 failed. Please check your settings and try again. Error details: {$a}';
$string['connect_heading'] = 'Automatic Connection';

// LTI Management Page
$string['ltimanagement'] = 'LTI Management';
$string['lticonnected'] = 'The Skill5 LTI tool is connected and active.';
$string['ltinotconnected'] = 'The Skill5 LTI tool is not connected. Please go to the main settings page to connect.';
$string['connect'] = 'Go to connection page';

// LTI Management Page - Revamped
$string['connectiondetails'] = 'Connection Details';
$string['ltitoolinfo'] = 'LTI Tool Information';
$string['skill5userinfo'] = 'Skill5 User Information';
$string['label_clientid'] = 'Client ID';
$string['label_adminemail'] = 'Administrator Email';
$string['label_entityuserid'] = 'Skill5 Entity User ID';
$string['nextsteps'] = 'Next Steps';
$string['step1_heading'] = 'Step 1: Enable the Tool';
$string['step1_text'] = 'The Skill5 LTI tool has been created, but it is disabled by default. You need to enable it to make it available for teachers in courses.';
$string['step1_instruction'] = 'Go to {$a} and click the \'eye\' icon to enable the \'Skill5 LTI Tool\'.';
$string['managetools_link_text'] = 'Manage tools';
$string['step2_heading'] = 'Step 2: Add the Tool to a Course';
$string['step2_text'] = 'Once enabled, you or your teachers can add the Skill5 tool to any course.';
$string['step2_instruction_1'] = 'Navigate to a course and turn \'Edit mode\' on.';
$string['step2_instruction_2'] = 'Click \'Add an activity or resource\' and select \'Skill5 LTI Tool\' from the list.';
$string['step2_instruction_3'] = 'Click the \'Select content\' button. This will open the Skill5 content library, allowing you to choose the course you want to link.';
$string['step2_instruction_4'] = 'Save the activity. Students can now access the Skill5 content directly from the Moodle course.';

// Settings Page - Revamped
$string['settings_intro_heading'] = 'Connect your Moodle to Skill5';
$string['settings_intro_text'] = 'Enter your Skill5 administrator email below and click the connect button. This will automatically create and configure the LTI 1.3 tool for you.';
$string['connection_established_heading'] = 'Connection Established';
$string['connection_established_text'] = 'A Skill5 Connection is already configured for this site.';
$string['connection_established_tip'] = 'You can view the connection details on the {$a}. If you need to generate a new connection, you must first delete the existing connection from the Moodle LTI tools page.';
$string['ltimanagement_link_text'] = 'LTI Management page';

// User Management Page
$string['usermanagement'] = 'User Management';

// Connection Assistant Page & Summary
$string['connectionassistant'] = 'Skill5 Connection Assistant';
$string['connectionstatus'] = 'Connection Status';
$string['summary_connected'] = 'The connection to Skill5 is active for the user: {$a}.';
$string['summary_connected_tip'] = 'To manage the connection, go to the {$a}.';
$string['summary_not_connected'] = 'The connection to Skill5 is not configured.';
$string['summary_not_connected_tip'] = 'To get started, go to the {$a}.';

