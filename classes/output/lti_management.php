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
 * LTI Management renderable class.
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
 * LTI Management page renderable.
 */
class lti_management implements renderable, templatable {
    
    /** @var stdClass LTI tool record */
    private $tool;
    
    /** @var string Admin email */
    private $admin_email;
    
    /** @var string Entity user ID */
    private $entityuser_id;
    
    /**
     * Constructor.
     *
     * @param stdClass $tool LTI tool record
     * @param string $admin_email Admin email
     * @param string $entityuser_id Entity user ID
     */
    public function __construct($tool, $admin_email, $entityuser_id) {
        $this->tool = $tool;
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
        global $CFG;
        
        $data = new stdClass();
        
        // Connection details section.
        $data->connectiondetails = new stdClass();
        $data->connectiondetails->ltitoolinfo = new stdClass();
        $data->connectiondetails->ltitoolinfo->clientid = $this->tool->clientid;
        
        $data->connectiondetails->skill5userinfo = new stdClass();
        $data->connectiondetails->skill5userinfo->adminemail = $this->admin_email;
        $data->connectiondetails->skill5userinfo->entityuserid = $this->entityuser_id;
        
        // Next steps section.
        $data->nextsteps = new stdClass();
        
        // Step 1.
        $data->nextsteps->step1 = new stdClass();
        $manage_tools_url = new \moodle_url('/mod/lti/toolconfigure.php');
        $manage_tools_link = \html_writer::link($manage_tools_url, get_string('managetools_link_text', 'local_skill5'));
        $data->nextsteps->step1->instruction = get_string('step1_instruction', 'local_skill5', $manage_tools_link);
        
        // Step 2.
        $data->nextsteps->step2 = new stdClass();
        $data->nextsteps->step2->instructions = [
            get_string('step2_instruction_1', 'local_skill5'),
            get_string('step2_instruction_2', 'local_skill5'),
            get_string('step2_instruction_3', 'local_skill5'),
            get_string('step2_instruction_4', 'local_skill5'),
        ];
        
        return $data;
    }
}
