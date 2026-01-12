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

// Inject the handshake script directly into the page.
$handshake_script = <<<SCRIPT
<script>
(function() {
    'use strict';
    
    const config = {
        skill5Origin: '{$skill5_origin_url}',
        connectionUrl: '{$connection_assistant_url->out(false)}'
    };
    
    console.log('[Moodle] Handshake listener is active.');
    
    window.addEventListener('message', function receiveMessage(event) {
        if (event.origin !== config.skill5Origin) {
            console.warn('[Moodle] Message from unexpected origin ignored:', event.origin);
            return;
        }
        
        const message = event.data;
        console.log('[Moodle] Received message:', message);
        
        if (message && message.type) {
            switch (message.type) {
                case 'SKILL5_IFRAME_READY':
                    console.log('[Moodle] Received SKILL5_IFRAME_READY. Sending acknowledgment...');
                    event.source.postMessage({ type: 'MOODLE_LISTENER_READY' }, event.origin);
                    break;
                    
                case 'SKILL5_SEND_EMAIL':
                    if (message.payload && message.payload.email) {
                        const adminEmail = message.payload.email;
                        console.log('[Moodle] Received email payload:', adminEmail);
                        
                        // Make an AJAX call to connect.php to create the LTI tool
                        const connectUrl = '{$CFG->wwwroot}/local/skill5/connect.php';
                        console.log('[Moodle] Calling connect.php via fetch...');
                        
                        fetch(connectUrl + '?email=' + encodeURIComponent(adminEmail), {
                            method: 'GET',
                            credentials: 'same-origin'
                        })
                        .then(response => {
                            console.log('[Moodle] Connect.php response received');
                            if (response.ok) {
                                // Success! Redirect to the connection assistant
                                console.log('[Moodle] Redirecting to connection_assistant.php');
                                window.location.href = '{$CFG->wwwroot}/local/skill5/pages/connection_assistant.php';
                            } else {
                                console.error('[Moodle] Connect.php returned error:', response.status);
                                alert('{$CFG->wwwroot}/local/skill5/lang/en/local_skill5.php?string=error_connection_failed');
                            }
                        })
                        .catch(error => {
                            console.error('[Moodle] Error calling connect.php:', error);
                            alert('{$CFG->wwwroot}/local/skill5/lang/en/local_skill5.php?string=error_connection_failed');
                        });
                    } else {
                        console.error('[Moodle] Email payload is missing or invalid:', message.payload);
                    }
                    break;
            }
        }
    });
})();
</script>
SCRIPT;

echo $handshake_script;
echo $OUTPUT->footer();
