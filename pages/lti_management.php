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
    // --- Connection Details Box ---
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'connection-details');
    echo $OUTPUT->heading(get_string('connectiondetails', 'local_skill5'), 3);

    // LTI Tool Info.
    echo $OUTPUT->heading(get_string('ltitoolinfo', 'local_skill5'), 4, 'mdl-left');
    echo '<ul>';
    echo '<li><strong>' . get_string('label_clientid', 'local_skill5') . ':</strong> ' . $tool->clientid . '</li>';
    echo '</ul>';

    // Skill5 User Info.
    echo $OUTPUT->heading(get_string('skill5userinfo', 'local_skill5'), 4, 'mdl-left');
    echo '<ul>';
    echo '<li><strong>' . get_string('label_adminemail', 'local_skill5') . ':</strong> ' . $admin_email . '</li>';
    echo '<li><strong>' . get_string('label_entityuserid', 'local_skill5') . ':</strong> ' . $entityuser_id . '</li>';
    echo '</ul>';

    echo $OUTPUT->box_end();

    // --- Next Steps Box ---
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'next-steps');
    echo $OUTPUT->heading(get_string('nextsteps', 'local_skill5'), 3);

    // Step 1.
    echo $OUTPUT->heading(get_string('step1_heading', 'local_skill5'), 4);
    echo '<p>' . get_string('step1_text', 'local_skill5') . '</p>';
    $manage_tools_url = new moodle_url('/mod/lti/toolconfigure.php');
    $manage_tools_link = html_writer::link($manage_tools_url, get_string('managetools_link_text', 'local_skill5'));
    echo $OUTPUT->notification(get_string('step1_instruction', 'local_skill5', $manage_tools_link), 'info');

    // Step 2.
    echo $OUTPUT->heading(get_string('step2_heading', 'local_skill5'), 4);
    echo '<p>' . get_string('step2_text', 'local_skill5') . '</p>';
    echo '<ul>';
    echo '<li>' . get_string('step2_instruction_1', 'local_skill5') . '</li>';
    echo '<li>' . get_string('step2_instruction_2', 'local_skill5') . '</li>';
    echo '<li>' . get_string('step2_instruction_3', 'local_skill5') . '</li>';
    echo '<li>' . get_string('step2_instruction_4', 'local_skill5') . '</li>';
    echo '</ul>';

    echo $OUTPUT->box_end();

} else {
    // If the tool is not fully configured, redirect to the initial setup page.
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}

echo $OUTPUT->footer();
