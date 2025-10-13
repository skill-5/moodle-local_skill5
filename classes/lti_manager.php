<?php

namespace local_skill5;

defined('MOODLE_INTERNAL') || die();

class lti_manager {

    private const LTI_SERVER_URL = 'https://gvpnpeetmg.us-east-2.awsapprunner.com';
    private const API_LTI_JWT_SECRET = 'skill5_lti_jwt_key';

    /**
     * Creates and configures the LTI 1.3 tool programmatically.
     *
     * @return void
     * @throws \moodle_exception
     */
    public static function create_lti_tool() {
        global $CFG, $DB;

        // 1. Get admin email from settings.
        $admin_email = get_config('local_skill5', 'admin_email');

        if (empty($admin_email)) {
            throw new \moodle_exception('missingconfig', 'local_skill5');
        }

        // 2. Fetch the EntityUser ID from Skill5 API.
        $curl = new \curl();
        $options = [
            'httpheader' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . self::API_LTI_JWT_SECRET
            ]
        ];
        $response = $curl->post(api_manager::get_skill5_url() . '/api/plugins/moodle/register/info/entity-user', json_encode(['email' => $admin_email]), $options);

        if ($curl->info['http_code'] !== 200) {
            throw new \moodle_exception('connection_failed', 'local_skill5', '', null, 'Could not fetch EntityUser ID from Skill5 API. Response: ' . $response);
        }
        $entity_data = json_decode($response);
        if (empty($entity_data->entityUserId)) {
            throw new \moodle_exception('connection_failed', 'local_skill5', '', null, 'Invalid response from Skill5 API when fetching EntityUser ID.');
        }
        $entityuser_id = $entity_data->entityUserId;
        set_config('entityuserid', $entityuser_id, 'local_skill5');

        // 3. Define the LTI tool parameters.
        $tool = self::get_tool_definition(self::LTI_SERVER_URL);
        $toolconfig = self::get_tool_configuration(self::LTI_SERVER_URL);

        // 4. Create the LTI tool in Moodle.
        try {
            $newtoolid = lti_add_type($tool, $toolconfig);
            if (empty($newtoolid)) {
                throw new \moodle_exception('failedcreateltool', 'local_skill5', '', null, 'lti_add_type did not return a valid ID.');
            }
            $newtool = $DB->get_record('lti_types', ['id' => $newtoolid]);
            $clientid = $newtool->clientid;
        } catch (\Exception $e) {
            throw new \moodle_exception('failedcreateltool', 'local_skill5', '', null, 'Failed to create LTI tool: ' . $e->getMessage());
        }

        // 5. Register the platform on the LTI Server.
        self::register_platform_on_lti_server(self::LTI_SERVER_URL, $clientid, self::API_LTI_JWT_SECRET, $newtool->id);

        // 6. Register the Moodle instance on the Skill5 Application.
        self::register_moodle_on_skill5_app($clientid, $entityuser_id, self::API_LTI_JWT_SECRET, $newtool->id);

        // 7. Finalize.
        set_config('connection_status', 'success', 'local_skill5');
        redirect(new \moodle_url('/local/skill5/pages/landing.php'));
    }

    /**
     * Returns the base LTI tool definition object.
     *
     * @param string $lti_server_url
     * @return \stdClass
     */
    private static function get_tool_definition(string $lti_server_url): \stdClass {
        $tool = new \stdClass();
        $tool->name = 'Skill5 LTI Tool';
        $tool->description = 'LTI Tool for integration with the Skill5 platform.';
        $tool->toolurl = $lti_server_url . '/api/public/lti';
        $tool->baseurl = $lti_server_url . '/api/public/lti';
        $tool->ltiversion = '1.3.0';
        $tool->icon = 'https://i.ibb.co/fzJMWnVf/logo.png';
        $tool->secureicon = 'https://i.ibb.co/fzJMWnVf/logo.png';
        $tool->issuer = $lti_server_url;
        return $tool;
    }

    /**
     * Returns the LTI tool configuration object.
     *
     * @param string $lti_server_url
     * @return \stdClass
     */
    private static function get_tool_configuration(string $lti_server_url): \stdClass {
        $toolconfig = new \stdClass();
        $toolconfig->lti_publickeytype = 'keyseturl';
        $toolconfig->lti_publickeyset = $lti_server_url . '/api/public/lti/keys';
        $toolconfig->lti_initiatelogin = $lti_server_url . '/api/public/lti/login';
        $toolconfig->lti_redirectionuris = $lti_server_url . '/api/public/lti' . "\n" . $lti_server_url . '/content-selection';
        $toolconfig->lti_contentitem = 1;
        $toolconfig->lti_customparameters = '';
        $toolconfig->lti_sendcontext = 2;
        $toolconfig->lti_forceauth = 0;
        $toolconfig->lti_coursevisible = 2;
        $toolconfig->launchcontainer = LTI_LAUNCH_CONTAINER_EMBED; // Default to embed, providing a more integrated experience.

        // Services
        $toolconfig->ltiservice_gradesynchronization = 2;
        $toolconfig->ltiservice_memberships = 1;
        $toolconfig->ltiservice_toolsettings = 0;

        // Privacy
        $toolconfig->lti_sendname = 1;
        $toolconfig->lti_sendemailaddr = 1;
        $toolconfig->lti_acceptgrades = 1;
        $toolconfig->lti_forcessl = 0;

        // Miscellaneous
        $toolconfig->lti_organizationid_default = 1;

        return $toolconfig;
    }

    /**
     * Registers the Moodle platform on the LTI server.
     *
     * @param string $lti_server_url
     * @param string $clientid
     * @param string $api_lti_jwt_secret
     * @param int $newtoolid
     * @return void
     * @throws \moodle_exception
     */
    private static function register_platform_on_lti_server(string $lti_server_url, string $clientid, string $api_lti_jwt_secret, int $newtoolid) {
        global $CFG;
        $lti_server_payload = [
            'platformUrl' => $CFG->wwwroot,
            'clientId' => $clientid,
            'authEndpoint' => (new \moodle_url('/mod/lti/auth.php'))->out(false),
            'accesstokenEndpoint' => (new \moodle_url('/mod/lti/token.php'))->out(false),
            'authConfig' => [
                'method' => 'JWK_SET',
                'key' => (new \moodle_url('/mod/lti/certs.php'))->out(false)
            ]
        ];

        $curl_lti = new \curl();
        $options = [
            'httpheader' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_lti_jwt_secret
            ]
        ];
        $response_lti = $curl_lti->post($lti_server_url . '/platforms', json_encode($lti_server_payload), $options);
        $http_code_lti = $curl_lti->info['http_code'];

        if ($http_code_lti !== 201 && $http_code_lti !== 409) {
            lti_delete_type($newtoolid);
            $error_details = json_decode($response_lti);
            $error_message = $error_details->error ?? 'Unknown error from LTI Server.';
            throw new \moodle_exception('connectionfailed', 'local_skill5', '', null, 'Failed to register platform on LTI Server (HTTP ' . $http_code_lti . '): ' . $error_message);
        }
    }

    /**
     * Registers the Moodle instance on the Skill5 application.
     *
     * @param string $clientid
     * @param string $entityuser_id
     * @param string $api_lti_jwt_secret
     * @param int $newtoolid
     * @return void
     * @throws \moodle_exception
     */
    private static function register_moodle_on_skill5_app(string $clientid, string $entityuser_id, string $api_lti_jwt_secret, int $newtoolid) {
        global $CFG;
        $skill5_app_payload = [
            'clientId' => $clientid,
            'issuer' => $CFG->wwwroot,
            'entityUserId' => $entityuser_id
        ];

        $options = [
            'httpheader' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_lti_jwt_secret
            ]
        ];

        $curl_skill5 = new \curl();
        $response_skill5 = $curl_skill5->post(api_manager::get_skill5_url() . '/api/plugins/moodle/register', json_encode($skill5_app_payload), $options);

        if ($curl_skill5->info['http_code'] != 200 && $curl_skill5->info['http_code'] != 201) {
            lti_delete_type($newtoolid);
            // TODO: Add logic to un-register from the LTI server if this call fails.
            throw new \moodle_exception('connectionfailed', 'local_skill5', '', null, 'Failed to register Moodle on Skill5 App. Response: ' . $response_skill5);
        }
    }
}
