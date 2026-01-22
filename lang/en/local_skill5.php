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

/**
 * English language strings for Skill5 plugin.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

// LTI Management Page.
$string['ltimanagement'] = 'LTI Management';
$string['lticonnected'] = 'The Skill5 LTI tool is connected and active.';
$string['ltinotconnected'] = 'The Skill5 LTI tool is not connected. Please go to the main settings page to connect.';
$string['connect'] = 'Go to connection page';

// LTI Management Page - Revamped.
$string['connectiondetails'] = 'Connection Details';
$string['ltitoolinfo'] = 'LTI Tool Information';
$string['skill5userinfo'] = 'Skill5 User Information';
$string['label_clientid'] = 'Client ID';
$string['label_adminname'] = 'Administrator Name';
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

// Settings Page - Revamped.
$string['settings_intro_heading'] = 'Connect your Moodle to Skill5';
$string['settings_intro_text'] = 'Enter your Skill5 administrator email below and click the connect button. This will automatically create and configure the LTI 1.3 tool for you.';
$string['connection_established_heading'] = 'Connection Established';
$string['connection_established_text'] = 'A Skill5 Connection is already configured for this site.';
$string['connection_established_tip'] = 'You can view the connection details on the {$a}. If you need to generate a new connection, you must first delete the existing connection from the Moodle LTI tools page.';
$string['ltimanagement_link_text'] = 'LTI Management page';

// User Management Page.
$string['usermanagement'] = 'User Management';

// Shop Catalog Page.
$string['shopcatalog'] = 'Shop Catalog';

// LTI Catalog Page.
$string['lticatalog'] = 'LTI Catalog';

// Stripe Checkout Integration.
$string['stripe_redirecting'] = 'Redirecting to payment...';
$string['stripe_payment_error'] = 'Error processing payment. Please try again.';
$string['stripe_popup_blocked'] = 'Popup may be blocked. Please allow popups for this site.';
$string['stripe_checkout_opened'] = 'Payment window opened successfully.';
$string['stripe_checkout_failed'] = 'Failed to open payment window. Please try again.';

// Connection Assistant Page and Summary.
$string['connectionassistant'] = 'Skill5 Connection Assistant';
$string['connectionstatus'] = 'Connection Status';
$string['summary_connected'] = 'The connection to Skill5 is active for the user: {$a}.';
$string['summary_connected_tip'] = 'To manage the connection, go to the {$a}.';
$string['summary_not_connected'] = 'The connection to Skill5 is not configured.';
$string['summary_not_connected_tip'] = 'To get started, go to the {$a}.';

// Privacy API.
$string['privacy:metadata:skill5_lti'] = 'In order to integrate with the Skill5 platform, user data is exchanged with the external Skill5 LTI service.';
$string['privacy:metadata:skill5_lti:userid'] = 'The user ID is sent from Moodle to allow you to access your data on the Skill5 platform.';
$string['privacy:metadata:skill5_lti:fullname'] = 'Your full name is sent to the Skill5 platform to provide a personalized learning experience.';
$string['privacy:metadata:skill5_lti:email'] = 'Your email address is sent to the Skill5 platform to enable account identification and communication.';

// Error messages.
$string['error_api_jwt_secret'] = 'API JWT Secret not found in configuration. Please reconnect the plugin.';
$string['error_entity_user_id'] = 'Admin Entity User ID not found in config. Please reconnect the plugin.';
$string['error_invalid_response'] = 'Invalid response from Skill5 API when fetching EntityUser ID.';
$string['error_curl_request'] = 'cURL request failed with error: {$a}';
$string['error_api_request'] = 'API request to {$a->endpoint} failed with HTTP code {$a->httpcode}. Response: {$a->response}';
$string['error_missing_admin_email'] = 'Admin email not configured. Please configure the admin email in settings.';
$string['error_fetch_entity_data'] = 'Could not fetch Entity data from Skill5 API. Response: {$a}';
$string['error_missing_entity_fields'] = 'Invalid response from Skill5 API. Missing entityUserId, entityId or jwtSecret.';
$string['error_lti_no_id'] = 'lti_add_type did not return a valid ID.';
$string['error_lti_creation_failed'] = 'Failed to create LTI tool: {$a}';
$string['error_unknown_lti_server'] = 'Unknown error from LTI Server.';
$string['error_register_lti_platform'] = 'Failed to register platform on LTI Server (HTTP {$a->httpcode}): {$a->message}';
$string['error_register_skill5_app'] = 'Failed to register Moodle on Skill5 App. Response: {$a}';
$string['error_unexpected'] = 'An unexpected error occurred';
$string['error_fetch_user_details'] = 'Error fetching user details from Skill5';
$string['error_user_not_found'] = 'User not found.';
$string['error_fetch_users'] = 'Error fetching users from Skill5';
$string['error_connection_failed'] = 'Connection failed. Please try again or contact support.';

// LTI Tool.
$string['lti_tool_name'] = 'Skill5 LTI Tool';
$string['lti_tool_description'] = 'LTI Tool for integration with the Skill5 platform.';

// User details page.
$string['user_details_heading'] = 'User Details: {$a}';
$string['course_progress'] = 'Course Progress';
$string['course'] = 'Course';
$string['completed_at'] = 'Completed At';
$string['not_completed'] = '-';
$string['no_course_progress'] = 'No course progress found.';
$string['badges'] = 'Badges';
$string['badge'] = 'Badge';
$string['issued_at'] = 'Issued At';
$string['no_badges'] = 'No badges found.';
$string['certificates'] = 'Certificates';
$string['certificate'] = 'Certificate';
$string['no_certificates'] = 'No certificates found.';

// User management page.
$string['login_count'] = 'Login Count';
$string['last_login'] = 'Last Login';
$string['never'] = 'Never';
$string['view_details'] = 'View Details';
$string['no_users'] = 'No users found.';
