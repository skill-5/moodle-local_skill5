<?php

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) { // This ensures the settings are only available to site administrators.

    // 1. Create a parent navigation node for the plugin.
    $ADMIN->add('localplugins', new admin_category('local_skill5_category', 'Skill5 Moodle'));

    // 2. Define the main settings page and add it to the parent node.
    $settings = new admin_settingpage('local_skill5_settings', get_string('overview', 'local_skill5'));
    $ADMIN->add('local_skill5_category', $settings);

    // 3. Define the Connection Assistant page and add it to the parent node.
    $connection_assistant_page = new admin_externalpage(
        'local_skill5_connection_assistant',
        get_string('connectionassistant', 'local_skill5'),
        new moodle_url('/local/skill5/pages/connection_assistant.php')
    );
    $ADMIN->add('local_skill5_category', $connection_assistant_page);

    // 4. Define the LTI Management page and add it to the parent node.
    $lti_page = new admin_externalpage(
        'local_skill5_lti_management',
        get_string('ltimanagement', 'local_skill5'),
        new moodle_url('/local/skill5/pages/lti_management.php')
    );
    $ADMIN->add('local_skill5_category', $lti_page);

    // 5. Define the User Management page and add it to the parent node.
    $user_page = new admin_externalpage(
        'local_skill5_user_management',
        get_string('usermanagement', 'local_skill5'),
        new moodle_url('/local/skill5/pages/user_management.php')
    );
    $ADMIN->add('local_skill5_category', $user_page);

    // This code defines the content of the main settings page.
    // It only runs when the settings page is being displayed.
    // This code runs on all admin pages. We need to check if we are on a Skill5 page
    // before attempting to redirect.
    $tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);
    $category_param = optional_param('category', '', PARAM_ALPHANUMEXT);
    $section_param = optional_param('section', '', PARAM_ALPHANUMEXT);

    $is_on_skill5_category_page = ($category_param === 'local_skill5_category');
    $is_on_skill5_section_page = (strpos($section_param, 'local_skill5_') === 0);

    if (!$tool && ($is_on_skill5_category_page || $is_on_skill5_section_page)) {
        redirect(new moodle_url('/local/skill5/pages/landing.php'));
    }

    // This code defines the content of the main settings page ('Overview').
    if ($settings && $tool) {
        // --- STATE: CONNECTED ---
        $admin_email = get_config('local_skill5', 'admin_email');
        $connection_assistant_url = new moodle_url('/local/skill5/pages/connection_assistant.php');
        $connection_assistant_link = html_writer::link($connection_assistant_url, get_string('connectionassistant', 'local_skill5'));
        $summary_text = get_string('summary_connected', 'local_skill5', $admin_email);
        $settings->add(new admin_setting_heading('skill5_summary', get_string('connectionstatus', 'local_skill5'), $summary_text));
        $tip_text = get_string('summary_connected_tip', 'local_skill5', $connection_assistant_link);
        $settings->add(new admin_setting_heading('skill5_summary_tip', '', $tip_text));
    }
}
