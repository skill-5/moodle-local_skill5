<?php

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) { // This ensures the settings are only available to site administrators.

    // 1. Create a parent navigation node for the plugin.
    $ADMIN->add('localplugins', new admin_category('local_skill5_category', 'Skill5 Moodle'));

    // 2. Define the main settings page and add it to the parent node.
    $settings = new admin_settingpage('local_skill5_settings', get_string('pluginname', 'local_skill5'));
    $ADMIN->add('local_skill5_category', $settings);

    // 3. Define the LTI Management page and add it to the parent node.
    $lti_page = new admin_externalpage('local_skill5_lti_management', get_string('ltimanagement', 'local_skill5'), new moodle_url('/local/skill5/lti_management.php'));
    $ADMIN->add('local_skill5_category', $lti_page);

    // --- Define the content of the main settings page ---

    // Check if the tool is already created.
    $tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);

    if ($tool) {
        // --- STATE: CONNECTED ---
        $settings->add(new admin_setting_heading('connection_established_heading', get_string('connection_established_heading', 'local_skill5'), get_string('connection_established_text', 'local_skill5')));

        // Display Skill5 User Info in a box.
        $admin_email = get_config('local_skill5', 'admin_email');
        $entityuser_id = get_config('local_skill5', 'entityuserid');

        $user_info_html = '<ul>';
        $user_info_html .= '<li><strong>' . get_string('label_adminemail', 'local_skill5') . ':</strong> ' . $admin_email . '</li>';
        $user_info_html .= '<li><strong>' . get_string('label_entityuserid', 'local_skill5') . ':</strong> ' . $entityuser_id . '</li>';
        $user_info_html .= '</ul>';
        $settings->add(new admin_setting_heading('skill5userinfo_display', get_string('skill5userinfo', 'local_skill5'), $user_info_html));

        // Display disabled button and tip.
        $disabled_button = html_writer::link('#', get_string('connect_button', 'local_skill5'), ['class' => 'btn btn-primary disabled', 'role' => 'button', 'aria-disabled' => 'true']);
        $settings->add(new admin_setting_heading('connect_button_disabled', '', $disabled_button));

        $lti_management_url = new moodle_url('/local/skill5/lti_management.php');
        $lti_management_link = html_writer::link($lti_management_url, get_string('ltimanagement_link_text', 'local_skill5'));
        $settings->add(new admin_setting_heading('connection_tip', '', html_writer::tag('div', get_string('connection_established_tip', 'local_skill5', $lti_management_link), ['class' => 'text-muted'])));

    } else {
        // --- STATE: NOT CONNECTED ---
        $settings->add(new admin_setting_heading('settings_intro_heading', get_string('settings_intro_heading', 'local_skill5'), get_string('settings_intro_text', 'local_skill5')));

        // Setting: Administrator Email.
        $settings->add(new admin_setting_configtext(
            'local_skill5/admin_email',
            get_string('admin_email', 'local_skill5'),
            get_string('admin_email_desc', 'local_skill5'),
            '',
            PARAM_EMAIL
        ));

        // Action: Connect button.
        $url = new moodle_url('/local/skill5/connect.php', ['sesskey' => sesskey()]);
        $link = html_writer::link($url, get_string('connect_button', 'local_skill5'), ['class' => 'btn btn-primary']);
        $settings->add(new admin_setting_heading('connect_heading', '', $link));
    }
}
