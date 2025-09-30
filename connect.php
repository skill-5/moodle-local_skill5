<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once(__DIR__ . '/classes/lti_manager.php');

use local_skill5\lti_manager;

// Check for required capabilities.
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Defer the business logic to the LTI manager class.
try {
    lti_manager::create_lti_tool();
} catch (\moodle_exception $e) {
    // Catch and display any Moodle exceptions.
    print_error($e->errorcode, $e->module, $e->a, $e->debuginfo);
} catch (\Exception $e) {
    // Catch any other generic exceptions.
    echo $OUTPUT->header();
    echo $OUTPUT->notification('An unexpected error occurred: ' . $e->getMessage());
    echo $OUTPUT->footer();
}
