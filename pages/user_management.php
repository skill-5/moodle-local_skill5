<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Ensure the user has the required capability.
admin_externalpage_setup('local_skill5_user_management');

// Check if the tool is configured. If not, redirect to the landing page.
$tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);
if (!$tool) {
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}

// Start page output.
echo $OUTPUT->header();

$heading = get_string('usermanagement', 'local_skill5');
echo $OUTPUT->heading($heading);

// Fetch users from the Skill5 API.
try {
    $users = local_skill5\api_manager::get_users();
} catch (Exception $e) {
    echo $OUTPUT->notification(get_string('error_fetch_users', 'local_skill5') . ': ' . $e->getMessage());
    echo $OUTPUT->footer();
    exit;
}

// Render using template.
$renderable = new \local_skill5\output\user_management($users);
echo $OUTPUT->render($renderable);

// End page output.
echo $OUTPUT->footer();
