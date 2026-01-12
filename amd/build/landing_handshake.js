define(["exports", "core/ajax", "core/notification"], function (_exports, _ajax, _notification) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.init = void 0;
  _ajax = _interopRequireDefault(_ajax);
  _notification = _interopRequireDefault(_notification);
  function _interopRequireDefault(e) { return e && e.__esModule ? e : { "default": e }; }
  /**
   * Landing page handshake module for Skill5 integration.
   *
   * @module     local_skill5/landing_handshake
   * @copyright  2025 Skill5
   * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
   */

  /**
   * Initialize the handshake listener for Skill5 iframe communication.
   *
   * @param {string} skill5Origin The origin URL of the Skill5 platform
   * @param {string} connectUrl The URL to connect.php
   * @param {string} connectionAssistantUrl The URL to connection_assistant.php
   */
  var init = _exports.init = function init(skill5Origin, connectUrl, connectionAssistantUrl) {
    window.console.log('[Moodle] Handshake listener is active.');
    window.addEventListener('message', function (event) {
      if (event.origin !== skill5Origin) {
        window.console.warn('[Moodle] Message from unexpected origin ignored:', event.origin);
        return;
      }
      var message = event.data;
      window.console.log('[Moodle] Received message:', message);
      if (message && message.type) {
        switch (message.type) {
          case 'SKILL5_IFRAME_READY':
            window.console.log('[Moodle] Received SKILL5_IFRAME_READY. Sending acknowledgment...');
            event.source.postMessage({
              type: 'MOODLE_LISTENER_READY'
            }, event.origin);
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
  var handleEmailPayload = function handleEmailPayload(adminEmail, connectUrl, connectionAssistantUrl) {
    window.console.log('[Moodle] Received email payload:', adminEmail);
    window.console.log('[Moodle] Calling connect.php via fetch...');
    var url = "".concat(connectUrl, "?email=").concat(encodeURIComponent(adminEmail));
    fetch(url, {
      method: 'GET',
      credentials: 'same-origin'
    }).then(function (response) {
      window.console.log('[Moodle] Connect.php response received');
      if (response.ok) {
        window.console.log('[Moodle] Redirecting to connection_assistant.php');
        window.location.href = connectionAssistantUrl;
      } else {
        window.console.error('[Moodle] Connect.php returned error:', response.status);
        _notification["default"].alert('Connection Error', 'Connection failed. Please try again or contact support.', 'OK');
      }
    })["catch"](function (error) {
      window.console.error('[Moodle] Error calling connect.php:', error);
      _notification["default"].alert('Connection Error', 'Connection failed. Please try again or contact support.', 'OK');
    });
  };
});
