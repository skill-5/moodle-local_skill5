<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/skill5/classes/api_manager.php');

admin_externalpage_setup('local_skill5_connection_assistant');


$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_url('/local/skill5/connection_assistant.php');
$PAGE->set_title(get_string('connectionassistant', 'local_skill5'));
$PAGE->set_heading(get_string('connectionassistant', 'local_skill5'));

echo $OUTPUT->header();

// Check if an email is provided in the URL.
$email_from_url = optional_param('email', '', PARAM_EMAIL);

// Check if the tool is configured. If not, and no email is provided, redirect to the landing page.
$tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);
if (!$tool && empty($email_from_url)) {
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}

// Check if the tool is already created.
$tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);

if ($tool) {
    // --- STATE: CONNECTED ---
    echo $OUTPUT->box_start('generalbox boxaligncenter');
    echo $OUTPUT->heading(get_string('connection_established_heading', 'local_skill5'), 3);
    echo '<p>' . get_string('connection_established_text', 'local_skill5') . '</p>';

    // Display Skill5 User Info.
    $admin_email = get_config('local_skill5', 'admin_email');
    $entityuser_id = get_config('local_skill5', 'entityuserid');

    echo $OUTPUT->heading(get_string('skill5userinfo', 'local_skill5'), 4);
    $user_info_html = '<ul>';
    $user_info_html .= '<li><strong>' . get_string('label_adminemail', 'local_skill5') . ':</strong> ' . $admin_email . '</li>';
    $user_info_html .= '<li><strong>' . get_string('label_entityuserid', 'local_skill5') . ':</strong> ' . $entityuser_id . '</li>';
    $user_info_html .= '</ul>';
    echo $user_info_html;

    // Display tip.
    $lti_management_url = new moodle_url('/local/skill5/pages/lti_management.php');
    $lti_management_link = html_writer::link($lti_management_url, get_string('ltimanagement_link_text', 'local_skill5'));
    echo html_writer::tag('div', get_string('connection_established_tip', 'local_skill5', $lti_management_link), ['class' => 'text-muted', 'style' => 'margin-top: 15px;']);

    echo $OUTPUT->box_end();
} else if (!empty($email_from_url)) {
    // --- STATE: EMAIL RECEIVED, AUTO-CONNECTING ---
    // Save the email and proceed to the connection script.
    set_config('admin_email', $email_from_url, 'local_skill5');
    redirect(new moodle_url('/local/skill5/connect.php'));
} else {
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}

echo $OUTPUT->footer();
