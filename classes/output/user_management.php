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
 * User Management renderable class.
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
 * User Management page renderable.
 */
class user_management implements renderable, templatable {
    
    /** @var array Users data */
    private $users;
    
    /**
     * Constructor.
     *
     * @param array $users Array of user objects from API
     */
    public function __construct($users) {
        $this->users = $users;
    }
    
    /**
     * Export data for template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        
        if (!empty($this->users)) {
            $data->users = new stdClass();
            $data->users->list = [];
            
            foreach ($this->users as $user) {
                $last_login = $user->lastLoginAt ? userdate(strtotime($user->lastLoginAt)) : get_string('never', 'local_skill5');
                $details_url = new \moodle_url('/local/skill5/user_details.php', ['id' => $user->entityUserId]);
                
                $data->users->list[] = (object)[
                    'name' => $user->name,
                    'email' => $user->email,
                    'logincount' => $user->loginCount,
                    'lastlogin' => $last_login,
                    'detailsurl' => $details_url->out(false),
                    'detailstext' => get_string('view_details', 'local_skill5')
                ];
            }
        }
        
        return $data;
    }
}
