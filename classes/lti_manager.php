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
 * LTI manager class for Skill5 plugin.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_skill5;

/**
 * LTI manager class for Skill5 integration.
 *
 * Manages LTI tool creation and configuration.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lti_manager {
    /** @var string LTI server base URL. */
    private const LTI_SERVER_URL = 'https://lti.skill5.com';

    /**
     * Creates and configures the LTI 1.3 tool programmatically.
     *
     * @return void
     * @throws \moodle_exception
     */
    public static function create_lti_tool() {
        global $CFG, $DB;

        // 1. Get admin email from settings.
        $adminemail = get_config('local_skill5', 'admin_email');

        if (empty($adminemail)) {
            throw new \moodle_exception('error_missing_admin_email', 'local_skill5');
        }

        // 2. Fetch the EntityUser ID, Entity ID and JWT Secret from Skill5 API.
        $curl = new \curl();
        $options = [
            'httpheader' => [
                'Content-Type: application/json',
            ],
        ];
        $apiurl = api_manager::get_skill5_url() . '/api/plugins/moodle/register/info/entity-user';
        $response = $curl->post($apiurl, json_encode(['email' => $adminemail]), $options);

        if ($curl->info['http_code'] !== 200) {
            throw new \moodle_exception('error_fetch_entity_data', 'local_skill5', '', null, $response);
        }
        $entitydata = json_decode($response);
        if (empty($entitydata->entityUserId) || empty($entitydata->entityId) || empty($entitydata->jwtSecret)) {
            throw new \moodle_exception('error_missing_entity_fields', 'local_skill5');
        }
        $entityuserid = $entitydata->entityUserId;
        $entityid = $entitydata->entityId;
        $jwtsecret = $entitydata->jwtSecret;
        $adminname = !empty($entitydata->name) ? $entitydata->name : '';

        set_config('entityuserid', $entityuserid, 'local_skill5');
        set_config('entityid', $entityid, 'local_skill5');
        set_config('api_jwt_secret', $jwtsecret, 'local_skill5');
        if (!empty($adminname)) {
            set_config('admin_name', $adminname, 'local_skill5');
        }

        // 3. Define the LTI tool parameters.
        $tool = self::get_tool_definition(self::LTI_SERVER_URL);
        $toolconfig = self::get_tool_configuration(self::LTI_SERVER_URL);

        // 4. Create the LTI tool in Moodle.
        try {
            $newtoolid = lti_add_type($tool, $toolconfig);
            if (empty($newtoolid)) {
                throw new \moodle_exception('error_lti_no_id', 'local_skill5');
            }
            $newtool = $DB->get_record('lti_types', ['id' => $newtoolid]);
            $clientid = $newtool->clientid;
        } catch (\Exception $e) {
            throw new \moodle_exception('error_lti_creation_failed', 'local_skill5', '', null, $e->getMessage());
        }

        // 5. Register the platform on the LTI Server.
        self::register_platform_on_lti_server(self::LTI_SERVER_URL, $clientid, $newtool->id);

        // 6. Register the Moodle instance on the Skill5 Application.
        self::register_moodle_on_skill5_app($clientid, $entityuserid, $newtool->id);

        // 7. Finalize.
        set_config('connection_status', 'success', 'local_skill5');
        redirect(new \moodle_url('/local/skill5/pages/landing.php'));
    }

    /**
     * Returns the API JWT Secret from configuration.
     *
     * @return string
     * @throws \moodle_exception if secret is not configured
     */
    private static function get_api_jwt_secret(): string {
        $secret = get_config('local_skill5', 'api_jwt_secret');

        if (empty($secret)) {
            throw new \moodle_exception('error_api_jwt_secret', 'local_skill5');
        }

        return $secret;
    }

    /**
     * Returns the base LTI tool definition object.
     *
     * @param string $ltiserverurl The LTI server URL.
     * @return \stdClass The tool definition object.
     */
    private static function get_tool_definition(string $ltiserverurl): \stdClass {
        $tool = new \stdClass();
        $tool->name = get_string('lti_tool_name', 'local_skill5');
        $tool->description = get_string('lti_tool_description', 'local_skill5');
        $tool->toolurl = $ltiserverurl . '/api/public/lti';
        $tool->baseurl = $ltiserverurl . '/api/public/lti';
        $tool->ltiversion = '1.3.0';
        $tool->icon = 'https://i.ibb.co/fzJMWnVf/logo.png';
        $tool->secureicon = 'https://i.ibb.co/fzJMWnVf/logo.png';
        $tool->issuer = $ltiserverurl;
        return $tool;
    }

    /**
     * Returns the LTI tool configuration object.
     *
     * @param string $ltiserverurl The LTI server URL.
     * @return \stdClass The tool configuration object.
     */
    private static function get_tool_configuration(string $ltiserverurl): \stdClass {
        $toolconfig = new \stdClass();
        $toolconfig->lti_publickeytype = 'keyseturl';
        $toolconfig->lti_publickeyset = $ltiserverurl . '/api/public/lti/keys';
        $toolconfig->lti_initiatelogin = $ltiserverurl . '/api/public/lti/login';
        $toolconfig->lti_redirectionuris = $ltiserverurl . '/api/public/lti' . "\n" . $ltiserverurl . '/content-selection';
        $toolconfig->lti_contentitem = 1;
        $toolconfig->lti_customparameters = '';
        $toolconfig->lti_sendcontext = 2;
        $toolconfig->lti_forceauth = 0;
        $toolconfig->lti_coursevisible = 2;
        // Default to embed, providing a more integrated experience.
        $toolconfig->launchcontainer = LTI_LAUNCH_CONTAINER_EMBED;

        // Services.
        $toolconfig->ltiservice_gradesynchronization = 2;
        $toolconfig->ltiservice_memberships = 1;
        $toolconfig->ltiservice_toolsettings = 0;

        // Privacy.
        $toolconfig->lti_sendname = 1;
        $toolconfig->lti_sendemailaddr = 1;
        $toolconfig->lti_acceptgrades = 1;
        $toolconfig->lti_forcessl = 0;

        // Miscellaneous.
        $toolconfig->lti_organizationid_default = 1;

        return $toolconfig;
    }

    /**
     * Registers the Moodle platform on the LTI server.
     *
     * @param string $ltiserverurl The LTI server URL.
     * @param string $clientid The LTI client ID.
     * @param int $newtoolid The new tool ID.
     * @return void
     * @throws \moodle_exception If registration fails.
     */
    private static function register_platform_on_lti_server(string $ltiserverurl, string $clientid, int $newtoolid) {
        global $CFG;
        $ltipayload = [
            'platformUrl' => $CFG->wwwroot,
            'clientId' => $clientid,
            'authEndpoint' => (new \moodle_url('/mod/lti/auth.php'))->out(false),
            'accesstokenEndpoint' => (new \moodle_url('/mod/lti/token.php'))->out(false),
            'authConfig' => [
                'method' => 'JWK_SET',
                'key' => (new \moodle_url('/mod/lti/certs.php'))->out(false),
            ],
        ];

        $curllti = new \curl();
        $options = [
            'httpheader' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . self::get_api_jwt_secret(),
            ],
        ];
        $responselti = $curllti->post($ltiserverurl . '/platforms', json_encode($ltipayload), $options);
        $httpcodelti = $curllti->info['http_code'];

        if ($httpcodelti !== 201 && $httpcodelti !== 409) {
            lti_delete_type($newtoolid);
            $errordetails = json_decode($responselti);
            $errormessage = $errordetails->error ?? get_string('error_unknown_lti_server', 'local_skill5');
            $errordata = (object)['httpcode' => $httpcodelti, 'message' => $errormessage];
            throw new \moodle_exception('error_register_lti_platform', 'local_skill5', '', $errordata);
        }
    }

    /**
     * Registers the Moodle instance on the Skill5 application.
     *
     * @param string $clientid The LTI client ID.
     * @param string $entityuserid The entity user ID.
     * @param int $newtoolid The new tool ID.
     * @return void
     * @throws \moodle_exception If registration fails.
     */
    private static function register_moodle_on_skill5_app(string $clientid, string $entityuserid, int $newtoolid) {
        global $CFG;
        $skill5payload = [
            'clientId' => $clientid,
            'issuer' => $CFG->wwwroot,
            'entityUserId' => $entityuserid,
        ];

        $options = [
            'httpheader' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . self::get_api_jwt_secret(),
            ],
        ];

        $curlskill5 = new \curl();
        $apiurl = api_manager::get_skill5_url() . '/api/plugins/moodle/register';
        $responseskill5 = $curlskill5->post($apiurl, json_encode($skill5payload), $options);

        if ($curlskill5->info['http_code'] != 200 && $curlskill5->info['http_code'] != 201) {
            lti_delete_type($newtoolid);
            throw new \moodle_exception('error_register_skill5_app', 'local_skill5', '', null, $responseskill5);
        }
    }
}
