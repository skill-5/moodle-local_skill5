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
    echo $OUTPUT->notification('Error fetching users from Skill5: ' . $e->getMessage());
    echo $OUTPUT->footer();
    exit;
}

// Define table headers.
$table = new html_table();
$table->head = [
    get_string('name'),
    get_string('email'),
    'Login Count',
    'Last Login',
    'Actions'
];

// Populate table rows.
if (!empty($users)) {
    foreach ($users as $user) {
        $last_login = $user->lastLoginAt ? userdate(strtotime($user->lastLoginAt)) : 'Never';
        $details_url = new moodle_url('/local/skill5/user_details.php', ['id' => $user->entityUserId]);
        $details_link = html_writer::link($details_url, 'View Details');

        $row = new html_table_row([
            $user->name,
            $user->email,
            $user->loginCount,
            $last_login,
            $details_link
        ]);
        $table->data[] = $row;
    }
}

echo html_writer::table($table);

// End page output.
echo $OUTPUT->footer();
