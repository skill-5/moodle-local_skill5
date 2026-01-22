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
 * Plugin settings for Skill5 plugin.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // This ensures the settings are only available to site administrators.
    // 1. Create a parent navigation node for the plugin.
    $ADMIN->add('localplugins', new admin_category('local_skill5_category', 'Skill5 Moodle'));

    // 2. Define the main settings page and add it to the parent node.
    $settings = new admin_settingpage('local_skill5_settings', get_string('overview', 'local_skill5'));
    $ADMIN->add('local_skill5_category', $settings);

    // 3. Define the Connection Assistant page and add it to the parent node.
    $connectionassistantpage = new admin_externalpage(
        'local_skill5_connection_assistant',
        get_string('connectionassistant', 'local_skill5'),
        new moodle_url('/local/skill5/pages/connection_assistant.php')
    );
    $ADMIN->add('local_skill5_category', $connectionassistantpage);

    // 4. Define the LTI Management page and add it to the parent node.
    $ltipage = new admin_externalpage(
        'local_skill5_lti_management',
        get_string('ltimanagement', 'local_skill5'),
        new moodle_url('/local/skill5/pages/lti_management.php')
    );
    $ADMIN->add('local_skill5_category', $ltipage);

    // 5. Define the User Management page and add it to the parent node.
    $userpage = new admin_externalpage(
        'local_skill5_user_management',
        get_string('usermanagement', 'local_skill5'),
        new moodle_url('/local/skill5/pages/user_management.php')
    );
    $ADMIN->add('local_skill5_category', $userpage);

    // 6. Define the Shop Catalog page and add it to the parent node.
    $shopcatalogpage = new admin_externalpage(
        'local_skill5_shop_catalog',
        get_string('shopcatalog', 'local_skill5'),
        new moodle_url('/local/skill5/pages/shop_catalog.php')
    );
    $ADMIN->add('local_skill5_category', $shopcatalogpage);

    // 7. Define the LTI Catalog page and add it to the parent node.
    $lticatalogpage = new admin_externalpage(
        'local_skill5_lti_catalog',
        get_string('lticatalog', 'local_skill5'),
        new moodle_url('/local/skill5/pages/lti_catalog.php')
    );
    $ADMIN->add('local_skill5_category', $lticatalogpage);

    // This code runs on all admin pages. We need to check if we are on a Skill5 page.
    $tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);
    $categoryparam = optional_param('category', '', PARAM_ALPHANUMEXT);
    $sectionparam = optional_param('section', '', PARAM_ALPHANUMEXT);

    $isonskill5categorypage = ($categoryparam === 'local_skill5_category');
    $isonskill5sectionpage = (strpos($sectionparam, 'local_skill5_') === 0);

    if (!$tool && ($isonskill5categorypage || $isonskill5sectionpage)) {
        redirect(new moodle_url('/local/skill5/pages/landing.php'));
    }

    // This code defines the content of the main settings page ('Overview').
    if ($settings && $tool) {
        // State: Connected.
        $adminemail = get_config('local_skill5', 'admin_email');
        $connectionassistanturl = new moodle_url('/local/skill5/pages/connection_assistant.php');
        $connectionassistantlink = html_writer::link(
            $connectionassistanturl,
            get_string('connectionassistant', 'local_skill5')
        );
        $summarytext = get_string('summary_connected', 'local_skill5', $adminemail);
        $settings->add(new admin_setting_heading(
            'skill5_summary',
            get_string('connectionstatus', 'local_skill5'),
            $summarytext
        ));
        $tiptext = get_string('summary_connected_tip', 'local_skill5', $connectionassistantlink);
        $settings->add(new admin_setting_heading('skill5_summary_tip', '', $tiptext));
    }
}
