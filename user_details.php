<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Get the user ID from the URL.
$entity_user_id = required_param('id', PARAM_RAW);

// Ensure the user has the required capability.
admin_externalpage_setup('local_skill5_user_management'); // Re-using the same capability.

// Start page output.
echo $OUTPUT->header();

// Fetch user details from the Skill5 API.
try {
    $user_details = local_skill5\api_manager::get_user_details($entity_user_id);
} catch (Exception $e) {
    echo $OUTPUT->notification(get_string('error_fetch_user_details', 'local_skill5') . ': ' . $e->getMessage());
    echo $OUTPUT->footer();
    exit;
}

if (empty($user_details)) {
    echo $OUTPUT->notification(get_string('error_user_not_found', 'local_skill5'));
    echo $OUTPUT->footer();
    exit;
}

// Display user's basic info.
$heading = get_string('user_details_heading', 'local_skill5', htmlspecialchars($user_details->name));
echo $OUTPUT->heading($heading);

echo $OUTPUT->box_start();
echo '<p><strong>' . get_string('email') . ':</strong> ' . htmlspecialchars($user_details->email) . '</p>';
echo $OUTPUT->box_end();

// --- Course Progress Table ---
echo $OUTPUT->heading(get_string('course_progress', 'local_skill5'), 3);
$progress_table = new html_table();
$progress_table->head = [get_string('course', 'local_skill5'), get_string('progress'), get_string('completed_at', 'local_skill5')];

if (!empty($user_details->courseProgress)) {
    foreach ($user_details->courseProgress as $enrollment) {
        $completed_at = $enrollment->completedAt ? userdate(strtotime($enrollment->completedAt)) : get_string('not_completed', 'local_skill5');
        $progress_table->data[] = new html_table_row([
            htmlspecialchars($enrollment->name),
            htmlspecialchars($enrollment->progress),
            $completed_at
        ]);
    }
} else {
    $cell = new html_table_cell(get_string('no_course_progress', 'local_skill5'));
    $cell->colspan = 3;
    $row = new html_table_row([$cell]);
    $progress_table->data[] = $row;
}
echo html_writer::table($progress_table);

// --- Badges Table ---
echo $OUTPUT->heading(get_string('badges', 'local_skill5'), 3);
$badges_table = new html_table();
$badges_table->head = [get_string('badge', 'local_skill5'), get_string('issued_at', 'local_skill5')];

if (!empty($user_details->badges)) {
    foreach ($user_details->badges as $badge) {
        $badges_table->data[] = new html_table_row([
            htmlspecialchars($badge->name),
            userdate(strtotime($badge->createdAt))
        ]);
    }
} else {
    $cell = new html_table_cell(get_string('no_badges', 'local_skill5'));
    $cell->colspan = 2;
    $row = new html_table_row([$cell]);
    $badges_table->data[] = $row;
}
echo html_writer::table($badges_table);

// --- Certificates Table ---
echo $OUTPUT->heading(get_string('certificates', 'local_skill5'), 3);
$certs_table = new html_table();
$certs_table->head = [get_string('certificate', 'local_skill5'), get_string('issued_at', 'local_skill5')];

if (!empty($user_details->certificates)) {
    foreach ($user_details->certificates as $certificate) {
        $certs_table->data[] = new html_table_row([
            htmlspecialchars($certificate->name),
            userdate(strtotime($certificate->createdAt))
        ]);
    }
} else {
    $cell = new html_table_cell(get_string('no_certificates', 'local_skill5'));
    $cell->colspan = 2;
    $row = new html_table_row([$cell]);
    $certs_table->data[] = $row;
}
echo html_writer::table($certs_table);

// End page output.
echo $OUTPUT->footer();
