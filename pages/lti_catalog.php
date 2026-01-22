<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * LTI catalog page for Skill5 plugin.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_skill5\api_manager;

admin_externalpage_setup('local_skill5_lti_catalog');

global $CFG, $PAGE, $OUTPUT, $DB;

// Check if the tool is configured. If not, redirect to the landing page.
$tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);
if (!$tool) {
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}

$skill5url = api_manager::get_skill5_url();
$skill5originurl = rtrim($skill5url, '/');
$moodleorigin = $CFG->wwwroot;
$connectionassistanturl = new moodle_url('/local/skill5/pages/connection_assistant.php');

// Build the iframe URL with moodleOrigin parameter for LTI catalog plugin.
$iframesrc = $skill5url . '/plugin/config/lti-catalog?moodleOrigin=' . urlencode($moodleorigin);

// Debug: Log the URLs being used.
debugging('[Skill5 LTI Catalog] Moodle Origin: ' . $moodleorigin, DEBUG_DEVELOPER);
debugging('[Skill5 LTI Catalog] Skill5 URL: ' . $skill5url, DEBUG_DEVELOPER);
debugging('[Skill5 LTI Catalog] Iframe Source: ' . $iframesrc, DEBUG_DEVELOPER);

echo $OUTPUT->header();

$iframehtml = <<<HTML
<style>
    body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; }
    #skill5-lti-catalog-iframe-container { width: 100%; height: calc(100vh - 80px); border: none; padding-bottom: 100px; }
</style>
<iframe id="skill5-lti-catalog-iframe-container" src="{$iframesrc}"></iframe>
HTML;

echo $iframehtml;

// Initialize the handshake JavaScript module.
$connecturl = new moodle_url('/local/skill5/connect.php');
$PAGE->requires->js_call_amd('local_skill5/landing_handshake', 'init', [
    $skill5originurl,
    $connecturl->out(false),
    $connectionassistanturl->out(false),
]);
echo $OUTPUT->footer();
