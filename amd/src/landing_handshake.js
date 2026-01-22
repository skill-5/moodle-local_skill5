/**
 * Landing page handshake module for Skill5 integration.
 *
 * @module     local_skill5/landing_handshake
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';

/**
 * Initialize the handshake listener for Skill5 iframe communication.
 *
 * @param {string} skill5Origin The origin URL of the Skill5 platform
 * @param {string} connectUrl The URL to connect.php
 * @param {string} connectionAssistantUrl The URL to connection_assistant.php
 */
export const init = (skill5Origin, connectUrl, connectionAssistantUrl) => {
    window.console.log('[Moodle] Handshake listener is active. Skill5 Origin:', skill5Origin);

    window.addEventListener('message', (event) => {
        const message = event.data;

        // Log all incoming messages for debugging
        window.console.log('[Moodle] Received message from origin:', event.origin, 'Message:', message);

        // Handle payment completion from popup (may come from Skill5 origin)
        if (message && message.type === 'SKILL5_PAYMENT_COMPLETED') {
            // Validate origin is from Skill5
            if (event.origin === skill5Origin || event.origin.includes('skill5.com')) {
                window.console.log('[Moodle] Payment completed, forwarding to iframe:', message.payload);
                forwardMessageToIframe(message, skill5Origin);
            } else {
                window.console.warn('[Moodle] Payment message from unexpected origin:', event.origin);
            }
            return;
        }

        // For other messages, strict origin check
        if (event.origin !== skill5Origin) {
            // Only warn if it looks like a Skill5 message
            if (message && message.type && message.type.startsWith('SKILL5_')) {
                window.console.warn('[Moodle] Message from unexpected origin ignored:', event.origin);
            }
            return;
        }

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

                case 'SKILL5_OPEN_STRIPE_CHECKOUT':
                    if (message.payload && message.payload.url) {
                        handleStripeCheckout(message.payload, skill5Origin);
                    } else {
                        window.console.error('[Moodle] Stripe checkout payload is missing or invalid:', message.payload);
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
            return true;
        }
        window.console.error('[Moodle] Connect.php returned error:', response.status);
        return Notification.alert(
            'Connection Error',
            'Connection failed. Please try again or contact support.',
            'OK'
        );
    })
    .catch(error => {
        window.console.error('[Moodle] Error calling connect.php:', error);
        return Notification.alert(
            'Connection Error',
            'Connection failed. Please try again or contact support.',
            'OK'
        );
    });
};

/**
 * Forward message from popup to Skill5 iframe.
 *
 * @param {Object} message The message to forward
 * @param {string} targetOrigin The target origin for the message
 */
const forwardMessageToIframe = (message, targetOrigin) => {
    const iframeSelectors = [
        'iframe#skill5-shop-iframe-container',
        'iframe#skill5-lti-catalog-iframe-container',
        'iframe#skill5-iframe-container'
    ];

    window.console.log('[Moodle] Attempting to forward message to iframe. Target origin:', targetOrigin);
    window.console.log('[Moodle] Looking for iframes:', iframeSelectors);

    let iframeFound = false;

    for (const selector of iframeSelectors) {
        const skill5Iframe = document.querySelector(selector);
        window.console.log('[Moodle] Checking selector:', selector, 'Found:', !!skill5Iframe);

        if (skill5Iframe && skill5Iframe.contentWindow) {
            try {
                skill5Iframe.contentWindow.postMessage(message, targetOrigin);
                window.console.log('[Moodle] Message forwarded to iframe successfully:', selector);
                iframeFound = true;
                break;
            } catch (error) {
                window.console.error('[Moodle] Error forwarding message to iframe:', error);
            }
        }
    }

    if (!iframeFound) {
        window.console.error('[Moodle] Skill5 iframe not found. Cannot forward message.');
        // List all iframes on the page for debugging
        const allIframes = document.querySelectorAll('iframe');
        window.console.log('[Moodle] All iframes on page:', allIframes.length);
        allIframes.forEach((iframe, index) => {
            window.console.log('[Moodle] Iframe', index, '- ID:', iframe.id, 'Src:', iframe.src);
        });
    }
};

/**
 * Handle Stripe checkout payload from Skill5 iframe.
 *
 * @param {Object} payload The checkout payload containing URL and session information
 * @param {string} skill5Origin The origin URL of the Skill5 platform
 */
const handleStripeCheckout = (payload, skill5Origin) => {
    window.console.log('[Moodle] Received checkout request:', payload);

    if (payload.url) {
        const checkoutWindow = window.open(
            payload.url,
            'skill5_checkout',
            'width=600,height=700,scrollbars=yes,resizable=yes'
        );

        if (checkoutWindow) {
            checkoutWindow.focus();
            window.console.log('[Moodle] Checkout window opened successfully');
            showMoodleNotification('stripe_redirecting', 'info');

            // Monitor when popup closes and notify iframe to check payment status
            const checkPopupClosed = setInterval(() => {
                if (checkoutWindow.closed) {
                    clearInterval(checkPopupClosed);
                    window.console.log('[Moodle] Checkout popup closed, notifying iframe to check status');

                    // Send message to iframe that popup was closed - iframe should verify payment
                    const message = {
                        type: 'SKILL5_CHECKOUT_POPUP_CLOSED',
                        payload: {
                            sessionId: payload.sessionId,
                            checkoutSessionId: payload.checkoutSessionId
                        }
                    };
                    forwardMessageToIframe(message, skill5Origin);
                }
            }, 500);
        } else {
            window.console.warn('[Moodle] Popup may be blocked. Please allow popups for this site.');
            showMoodleNotification('stripe_popup_blocked', 'warning');
        }
    } else {
        window.console.error('[Moodle] Checkout URL not found in payload:', payload);
        showMoodleNotification('stripe_payment_error', 'error');
    }
};

/**
 * Show Moodle notification to the user.
 *
 * @param {string} messageKey The notification message key (language string identifier)
 * @param {string} type The notification type (info, success, warning, error)
 */
const showMoodleNotification = (messageKey, type = 'info') => {
    let message = messageKey;

    const messageMap = {
        'stripe_redirecting': 'Redirecting to payment...',
        'stripe_payment_error': 'Error processing payment. Please try again.',
        'stripe_popup_blocked': 'Popup may be blocked. Please allow popups for this site.',
        'stripe_checkout_opened': 'Payment window opened successfully.',
        'stripe_checkout_failed': 'Failed to open payment window. Please try again.'
    };

    if (messageMap[messageKey]) {
        message = messageMap[messageKey];
    }

    window.console.log(`[Moodle] [${type.toUpperCase()}] ${message}`);

    if (typeof Notification !== 'undefined' && Notification.addNotification) {
        Notification.addNotification({
            message: message,
            type: type
        });
    } else if (typeof M !== 'undefined' && M.util && M.util.add_notification) {
        M.util.add_notification({
            message: message,
            type: type
        });
    } else {
        window.console.log(`[Moodle] Notification: ${message}`);
    }
};
