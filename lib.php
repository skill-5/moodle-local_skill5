<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Called after the plugin is installed or updated.
 */
function local_skill5_after_install() {
    // After installation, redirect the user to the initial setup/landing page.
    // This ensures the first thing they see is the connection screen.
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}
