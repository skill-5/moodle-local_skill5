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
 * Connection Assistant renderable class.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_skill5\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Connection Assistant page renderable.
 */
class connection_assistant implements renderable, templatable {
    
    /** @var string Admin name */
    private $admin_name;
    
    /** @var string Admin email */
    private $admin_email;
    
    /** @var string Entity user ID */
    private $entityuser_id;
    
    /**
     * Constructor.
     *
     * @param string $admin_name Admin name (can be empty)
     * @param string $admin_email Admin email
     * @param string $entityuser_id Entity user ID
     */
    public function __construct($admin_name, $admin_email, $entityuser_id) {
        $this->admin_name = $admin_name;
        $this->admin_email = $admin_email;
        $this->entityuser_id = $entityuser_id;
    }
    
    /**
     * Export data for template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        
        // Build user info array.
        $data->userinfo = [];
        
        if (!empty($this->admin_name)) {
            $data->userinfo[] = (object)[
                'label' => get_string('label_adminname', 'local_skill5'),
                'value' => $this->admin_name
            ];
        }
        
        $data->userinfo[] = (object)[
            'label' => get_string('label_adminemail', 'local_skill5'),
            'value' => $this->admin_email
        ];
        
        $data->userinfo[] = (object)[
            'label' => get_string('label_entityuserid', 'local_skill5'),
            'value' => $this->entityuser_id
        ];
        
        // Build tip with link.
        $lti_management_url = new \moodle_url('/local/skill5/pages/lti_management.php');
        $lti_management_link = \html_writer::link($lti_management_url, get_string('ltimanagement_link_text', 'local_skill5'));
        $data->tip = get_string('connection_established_tip', 'local_skill5', $lti_management_link);
        
        return $data;
    }
}
