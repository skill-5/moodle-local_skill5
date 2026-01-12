<?php
require_once(__DIR__ . '/../../../config.php');

use local_skill5\api_manager;

require_login();

global $CFG, $PAGE, $OUTPUT;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/skill5/pages/landing.php');
$PAGE->set_title(get_string('pluginname', 'local_skill5'));
$PAGE->set_heading(get_string('pluginname', 'local_skill5'));

$skill5_url = api_manager::get_skill5_url();
$skill5_origin_url = rtrim($skill5_url, '/');
$moodle_origin = $CFG->wwwroot;
$connection_assistant_url = new moodle_url('/local/skill5/pages/connection_assistant.php');

// Build the iframe URL with moodleOrigin parameter
$iframe_src = $skill5_url . '/plugin?moodleOrigin=' . urlencode($moodle_origin);

// Debug: Log the URLs being used
error_log('[Skill5 Plugin] Moodle Origin: ' . $moodle_origin);
error_log('[Skill5 Plugin] Skill5 URL: ' . $skill5_url);
error_log('[Skill5 Plugin] Iframe Source: ' . $iframe_src);

echo $OUTPUT->header();

$iframe_html = <<<HTML
<style>
    body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; }
    #skill5-iframe-container { width: 100%; height: calc(100vh - 80px); border: none; padding-bottom: 100px; }
</style>
<iframe id="skill5-iframe-container" src="{$iframe_src}"></iframe>
HTML;

echo $iframe_html;

// Initialize the handshake JavaScript module.
$connect_url = new moodle_url('/local/skill5/connect.php');
$PAGE->requires->js_call_amd('local_skill5/landing_handshake', 'init', [
    $skill5_origin_url,
    $connect_url->out(false),
    $connection_assistant_url->out(false)
]);
echo $OUTPUT->footer();
