<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once(__DIR__ . '/classes/lti_manager.php');

use local_skill5\lti_manager;

// Check for required capabilities.
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Check if an email is provided in the URL and save it.
$email_from_url = optional_param('email', '', PARAM_EMAIL);
if (!empty($email_from_url)) {
    set_config('admin_email', $email_from_url, 'local_skill5');
}

// Defer the business logic to the LTI manager class.
try {
    lti_manager::create_lti_tool();
    
    // Success! Return a JSON response for AJAX calls, or redirect for direct access.
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // AJAX request - return JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    } else {
        // Direct access - redirect
        redirect(new moodle_url('/local/skill5/pages/connection_assistant.php'));
    }
} catch (\moodle_exception $e) {
    // Return error response for AJAX, or display error page for direct access
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    } else {
        print_error($e->errorcode, $e->module, $e->a, $e->debuginfo);
    }
} catch (\Exception $e) {
    // Return error response for AJAX, or display error page for direct access
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->notification(get_string('error_unexpected', 'local_skill5') . ': ' . $e->getMessage());
        echo $OUTPUT->footer();
    }
}
