<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_skill5_lti_management');

$context = context_system::instance();
require_capability('moodle/site:config', $context);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('ltimanagement', 'local_skill5'));

// Fetch connection data.
$tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);
$admin_email = get_config('local_skill5', 'admin_email');
$entityuser_id = get_config('local_skill5', 'entityuserid');

if ($tool && $admin_email && $entityuser_id) {
    // Render using template.
    $renderable = new \local_skill5\output\lti_management($tool, $admin_email, $entityuser_id);
    echo $OUTPUT->render($renderable);
} else {
    // If the tool is not fully configured, redirect to the initial setup page.
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}

echo $OUTPUT->footer();
