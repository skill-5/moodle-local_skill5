<?php

namespace local_skill5;

defined('MOODLE_INTERNAL') || die();

class api_manager {

    private const SKILL5_URL = 'https://app.skill5.com';

    /**
     * Returns the base URL for the Skill5 API.
     *
     * @return string
     */
    public static function get_skill5_url(): string {
        return self::SKILL5_URL;
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
            throw new \moodle_exception('error', 'local_skill5', '', null, 
                'API JWT Secret not found in configuration. Please reconnect the plugin.');
        }
        
        return $secret;
    }

    /**
     * Fetches all users associated with a Moodle entity.
     *
     * @return array|null The list of users or null on failure.
     * @throws \moodle_exception
     */
    public static function get_users(): ?array {
        // The API now expects the admin's Entity User ID to identify the Moodle instance.
        $admin_entity_user_id = get_config('local_skill5', 'entityuserid');

        if (empty($admin_entity_user_id)) {
            throw new \moodle_exception('error', 'local_skill5', '', null, 'Admin Entity User ID not found in config. Please reconnect the plugin.');
        }

        $endpoint = self::SKILL5_URL . '/api/plugins/moodle/admin/users';
        $params = ['entityUserId' => $admin_entity_user_id];

        $response_data = self::send_request($endpoint, $params, 'GET');

        if (empty($response_data->data)) {
            return [];
        }

        return $response_data->data;
    }

    /**
     * Fetches details for a specific user.
     *
     * @param string $user_id The user's ID.
     * @return \stdClass|null The user details or null on failure.
     * @throws \moodle_exception
     */
    public static function get_user_details(string $entity_user_id): ?\stdClass {
        $endpoint = self::SKILL5_URL . '/api/plugins/moodle/admin/users/' . $entity_user_id;

        return self::send_request($endpoint, [], 'GET');
    }

    /**
     * Fetches the EntityUser ID from the Skill5 API for a given admin email.
     *
     * @param string $admin_email
     * @return string
     * @throws \moodle_exception
     */
    public static function get_entity_user_id(string $admin_email): string {
        $endpoint = self::SKILL5_URL . '/api/plugins/moodle/register/info/entity-user';
        $payload = ['email' => $admin_email];

        $response_data = self::send_request($endpoint, $payload, 'POST');

        if (empty($response_data->entityUserId)) {
            throw new \moodle_exception('connection_failed', 'local_skill5', '', null, 'Invalid response from Skill5 API when fetching EntityUser ID.');
        }

        return $response_data->entityUserId;
    }

    /**
     * Registers the Moodle instance on the Skill5 application.
     *
     * @param string $clientid
     * @param string $entityuser_id
     * @return void
     * @throws \moodle_exception
     */
    public static function register_moodle_on_skill5_app(string $clientid, string $entityuser_id): void {
        global $CFG;
        $endpoint = self::SKILL5_URL . '/api/plugins/moodle/register';
        $payload = [
            'clientId' => $clientid,
            'issuer' => $CFG->wwwroot,
            'entityUserId' => $entityuser_id
        ];

        self::send_request($endpoint, $payload, 'POST', [200, 201]);
    }

    /**
     * Sends a request to the Skill5 API.
     *
     * @param string $endpoint The API endpoint URL.
     * @param array $params The data/parameters for the request.
     * @param string $method The HTTP method (GET, POST, etc.).
     * @param array $expected_codes Expected successful HTTP status codes.
     * @return mixed The decoded JSON response.
     * @throws \moodle_exception
     */
    private static function send_request(string $endpoint, array $params = [], string $method = 'GET', array $expected_codes = [200]) {
        $curl = new \curl();
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . self::get_api_jwt_secret()
        ];

        $response = null;
        if ($method === 'POST') {
            $options = ['httpheader' => $headers];
            $response = $curl->post($endpoint, json_encode($params), $options);
        } else if ($method === 'GET') {
            $curl->setopt(['CURLOPT_HTTPHEADER' => $headers]);
            $response = $curl->get($endpoint, $params);
        }

        if ($response === false || $curl->get_errno()) {
            throw new \moodle_exception('connection_failed', 'local_skill5', '', null, "cURL request failed with error: " . $curl->get_error());
        }

        if (!in_array($curl->info['http_code'], $expected_codes)) {
            throw new \moodle_exception('connection_failed', 'local_skill5', '', null, "API request to {$endpoint} failed with HTTP code {$curl->info['http_code']}. Response: " . $response);
        }

        return json_decode($response);
    }
}
