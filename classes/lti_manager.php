<?php

namespace local_skill5;

defined('MOODLE_INTERNAL') || die();

class lti_manager {

    private const LTI_SERVER_URL = 'https://6as3yttb3k.us-east-1.awsapprunner.com';

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
            throw new \moodle_exception('error_missing_admin_email', 'local_skill5');
        }

        // 2. Fetch the EntityUser ID, Entity ID and JWT Secret from Skill5 API (no authentication required).
        $curl = new \curl();
        $options = [
            'httpheader' => [
                'Content-Type: application/json'
            ]
        ];
        $response = $curl->post(api_manager::get_skill5_url() . '/api/plugins/moodle/register/info/entity-user', json_encode(['email' => $admin_email]), $options);

        if ($curl->info['http_code'] !== 200) {
            throw new \moodle_exception('error_fetch_entity_data', 'local_skill5', '', null, $response);
        }
        $entity_data = json_decode($response);
        if (empty($entity_data->entityUserId) || empty($entity_data->entityId) || empty($entity_data->jwtSecret)) {
            throw new \moodle_exception('error_missing_entity_fields', 'local_skill5');
        }
        $entityuser_id = $entity_data->entityUserId;
        $entity_id = $entity_data->entityId;
        $jwt_secret = $entity_data->jwtSecret;
        $admin_name = !empty($entity_data->name) ? $entity_data->name : '';
        
        set_config('entityuserid', $entityuser_id, 'local_skill5');
        set_config('entityid', $entity_id, 'local_skill5');
        set_config('api_jwt_secret', $jwt_secret, 'local_skill5');
        if (!empty($admin_name)) {
            set_config('admin_name', $admin_name, 'local_skill5');
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
        self::register_moodle_on_skill5_app($clientid, $entityuser_id, $newtool->id);

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
     * @param string $lti_server_url
     * @return \stdClass
     */
    private static function get_tool_definition(string $lti_server_url): \stdClass {
        $tool = new \stdClass();
        $tool->name = get_string('lti_tool_name', 'local_skill5');
        $tool->description = get_string('lti_tool_description', 'local_skill5');
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
     * @param int $newtoolid
     * @return void
     * @throws \moodle_exception
     */
    private static function register_platform_on_lti_server(string $lti_server_url, string $clientid, int $newtoolid) {
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
                'Authorization: Bearer ' . self::get_api_jwt_secret()
            ]
        ];
        $response_lti = $curl_lti->post($lti_server_url . '/platforms', json_encode($lti_server_payload), $options);
        $http_code_lti = $curl_lti->info['http_code'];

        if ($http_code_lti !== 201 && $http_code_lti !== 409) {
            lti_delete_type($newtoolid);
            $error_details = json_decode($response_lti);
            $error_message = $error_details->error ?? get_string('error_unknown_lti_server', 'local_skill5');
            $error_data = (object)['httpcode' => $http_code_lti, 'message' => $error_message];
            throw new \moodle_exception('error_register_lti_platform', 'local_skill5', '', $error_data);
        }
    }

    /**
     * Registers the Moodle instance on the Skill5 application.
     *
     * @param string $clientid
     * @param string $entityuser_id
     * @param int $newtoolid
     * @return void
     * @throws \moodle_exception
     */
    private static function register_moodle_on_skill5_app(string $clientid, string $entityuser_id, int $newtoolid) {
        global $CFG;
        $skill5_app_payload = [
            'clientId' => $clientid,
            'issuer' => $CFG->wwwroot,
            'entityUserId' => $entityuser_id
        ];

        $options = [
            'httpheader' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . self::get_api_jwt_secret()
            ]
        ];

        $curl_skill5 = new \curl();
        $response_skill5 = $curl_skill5->post(api_manager::get_skill5_url() . '/api/plugins/moodle/register', json_encode($skill5_app_payload), $options);

        if ($curl_skill5->info['http_code'] != 200 && $curl_skill5->info['http_code'] != 201) {
            lti_delete_type($newtoolid);
            throw new \moodle_exception('error_register_skill5_app', 'local_skill5', '', null, $response_skill5);
        }
    }
}
