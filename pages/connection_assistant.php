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
    // Display Skill5 User Info.
    $admin_email = get_config('local_skill5', 'admin_email');
    $admin_name = get_config('local_skill5', 'admin_name');
    $entityuser_id = get_config('local_skill5', 'entityuserid');

    // Render using template.
    $renderable = new \local_skill5\output\connection_assistant($admin_name, $admin_email, $entityuser_id);
    echo $OUTPUT->render($renderable);
} else if (!empty($email_from_url)) {
    // --- STATE: EMAIL RECEIVED, AUTO-CONNECTING ---
    // Save the email and proceed to the connection script.
    set_config('admin_email', $email_from_url, 'local_skill5');
    redirect(new moodle_url('/local/skill5/connect.php'));
} else {
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}

echo $OUTPUT->footer();
