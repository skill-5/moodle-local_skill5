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
 * Privacy Subsystem implementation for local_skill5.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_skill5\privacy;

use core_privacy\local\metadata\collection;

/**
 * Privacy provider for local_skill5.
 *
 * This plugin sends user data to external services (Skill5 platform) via LTI.
 * It does not store personal user data in its own database tables.
 */
class provider implements
    \core_privacy\local\metadata\provider {

    /**
     * Returns metadata about the personal data stored by this plugin.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        // This plugin sends user data to the Skill5 external platform via LTI.
        $collection->add_external_location_link(
            'skill5_lti',
            [
                'userid' => 'privacy:metadata:skill5_lti:userid',
                'fullname' => 'privacy:metadata:skill5_lti:fullname',
                'email' => 'privacy:metadata:skill5_lti:email',
            ],
            'privacy:metadata:skill5_lti'
        );

        return $collection;
    }
}
