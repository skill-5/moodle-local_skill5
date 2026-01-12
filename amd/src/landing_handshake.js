/**
 * Landing page handshake module for Skill5 integration.
 *
 * @module     local_skill5/landing_handshake
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Initialize the handshake listener for Skill5 iframe communication.
 *
 * @param {string} skill5Origin The origin URL of the Skill5 platform
 * @param {string} connectUrl The URL to connect.php
 * @param {string} connectionAssistantUrl The URL to connection_assistant.php
 */
export const init = (skill5Origin, connectUrl, connectionAssistantUrl) => {
    window.console.log('[Moodle] Handshake listener is active.');

    window.addEventListener('message', (event) => {
        if (event.origin !== skill5Origin) {
            window.console.warn('[Moodle] Message from unexpected origin ignored:', event.origin);
            return;
        }

        const message = event.data;
        window.console.log('[Moodle] Received message:', message);

        if (message && message.type) {
            switch (message.type) {
                case 'SKILL5_IFRAME_READY':
                    window.console.log('[Moodle] Received SKILL5_IFRAME_READY. Sending acknowledgment...');
                    event.source.postMessage({type: 'MOODLE_LISTENER_READY'}, event.origin);
                    break;

                case 'SKILL5_SEND_EMAIL':
                    if (message.payload && message.payload.email) {
                        handleEmailPayload(message.payload.email, connectUrl, connectionAssistantUrl);
                    } else {
                        window.console.error('[Moodle] Email payload is missing or invalid:', message.payload);
                    }
                    break;
            }
        }
    });
};

/**
 * Handle the email payload from Skill5 iframe.
 *
 * @param {string} adminEmail The admin email received from Skill5
 * @param {string} connectUrl The URL to connect.php
 * @param {string} connectionAssistantUrl The URL to connection_assistant.php
 */
const handleEmailPayload = (adminEmail, connectUrl, connectionAssistantUrl) => {
    window.console.log('[Moodle] Received email payload:', adminEmail);
    window.console.log('[Moodle] Calling connect.php via fetch...');

    const url = `${connectUrl}?email=${encodeURIComponent(adminEmail)}`;

    fetch(url, {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => {
        window.console.log('[Moodle] Connect.php response received');
        if (response.ok) {
            window.console.log('[Moodle] Redirecting to connection_assistant.php');
            window.location.href = connectionAssistantUrl;
        } else {
            window.console.error('[Moodle] Connect.php returned error:', response.status);
            Notification.alert(
                'Connection Error',
                'Connection failed. Please try again or contact support.',
                'OK'
            );
        }
    })
    .catch(error => {
        window.console.error('[Moodle] Error calling connect.php:', error);
        Notification.alert(
            'Connection Error',
            'Connection failed. Please try again or contact support.',
            'OK'
        );
    });
};
