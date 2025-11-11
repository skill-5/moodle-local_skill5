# local_skill5

Moodle plugin that integrates Skill5 learning platform with Moodle through LTI 1.3, enabling seamless access to Skill5 courses and content directly from Moodle courses.

## Requirements

This plugin requires Moodle 4.1+

## Motivation for this plugin

Skill5 is a comprehensive learning platform that offers specialized courses and training content. This plugin bridges Moodle and Skill5, allowing educational institutions to:

- Integrate Skill5 courses directly into Moodle courses
- Provide single sign-on (SSO) access for students
- Maintain centralized user management in Moodle
- Track student progress and completion through LTI 1.3 standard

The plugin automates the complex LTI 1.3 configuration process, making it easy for administrators to connect their Moodle instance to Skill5 with just a few clicks.

## Installation

Install the plugin like any other plugin to folder:
```
/local/skill5
```

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins.

### Installation Steps

1. Download or clone this repository into the `/local/skill5` directory of your Moodle installation
2. Log in to your Moodle site as an administrator
3. Navigate to **Site administration → Notifications**
4. Follow the on-screen instructions to complete the installation
5. You will be automatically redirected to the Skill5 connection page

## Configuration

After installing the plugin, you need to connect it to your Skill5 account.

### Initial Setup

1. Navigate to **Site administration → Plugins → Local plugins → Skill5 Moodle**
2. You will see the **Connection Assistant** page
3. Enter your **Skill5 administrator email address**
4. Click the **"Connect with Skill5"** button
5. The plugin will automatically:
   - Fetch your Skill5 Entity User ID
   - Create an LTI 1.3 tool configuration
   - Register the tool with Skill5 API
   - Configure all necessary security settings

### Enabling the LTI Tool

After successful connection:

1. Navigate to **Site administration → Plugins → Activity modules → External tool → Manage tools**
2. Find **"Skill5 LTI Tool"** in the list
3. Click the **eye icon** to enable the tool
4. The tool is now available for teachers to add to courses

## Usage

### For Administrators

The plugin provides several management pages under **Site administration → Plugins → Local plugins → Skill5 Moodle**:

#### 1. Overview
Displays the current connection status and quick links to management pages.

#### 2. Connection Assistant
- Configure the initial connection to Skill5
- View connection details (Client ID, Administrator info)
- Reconnect if needed

#### 3. LTI Management
- View LTI tool configuration details
- Check connection status
- Access step-by-step instructions for enabling and using the tool

#### 4. User Management
- Manage user synchronization between Moodle and Skill5
- View user connection details

### For Teachers

Once the administrator has enabled the Skill5 LTI tool:

1. Navigate to your course
2. Turn **editing mode on**
3. Click **"Add an activity or resource"**
4. Select **"Skill5 LTI Tool"** from the External tool section
5. Configure the activity:
   - Give it a name
   - Click **"Select content"** to browse Skill5 courses
   - Choose the Skill5 course you want to link
6. Save the activity
7. Students can now access Skill5 content directly from your Moodle course

### For Students

Students simply click on the Skill5 activity within their Moodle course. They will be automatically logged into Skill5 using their Moodle credentials (SSO) and can access the linked content seamlessly.

## Dependencies and Permissions

### Required Moodle Components
- **mod_lti**: External tool (LTI) activity module (included in Moodle core)
- **enrol_lti**: LTI enrolment plugin (included in Moodle core)

### Required PHP Extensions
- cURL (for API communication with Skill5)
- JSON (for data processing)
- OpenSSL (for secure communication)

### Server Requirements
- HTTPS enabled (required for LTI 1.3 security)
- Outbound HTTPS connections allowed (to communicate with Skill5 API)

### Moodle Permissions
- Site administrators have full access to all plugin features
- Teachers can add Skill5 activities to their courses (after admin enables the tool)
- Students can access Skill5 content through course activities

## Capabilities

This plugin introduces the following capability:

### local/skill5:managedocuments
By default, only Moodle administrators can manage Skill5 connections and settings. This capability can be assigned to other roles if needed for delegated administration.

## API Integration

The plugin communicates with the Skill5 API to:

- Validate administrator credentials
- Retrieve Entity User IDs
- Register LTI 1.3 tool configurations
- Manage content selection and deep linking

All API communications are performed over HTTPS with proper authentication.

## Security Considerations

- The plugin uses **LTI 1.3** standard, which provides enhanced security over LTI 1.1
- All communications with Skill5 are encrypted using HTTPS
- OAuth 2.0 authentication is used for secure API access
- Client credentials are stored securely in Moodle's configuration
- The plugin follows Moodle security best practices

**Important**: Only authorized administrators should have access to the plugin configuration pages. The Skill5 administrator email and Entity User ID are sensitive credentials.

## Troubleshooting

### Connection Failed
If the connection to Skill5 fails:
1. Verify your Skill5 administrator email is correct
2. Check that your server can make outbound HTTPS connections
3. Ensure your Moodle site is accessible via HTTPS
4. Check PHP error logs for detailed error messages

### LTI Tool Not Appearing
If teachers cannot see the Skill5 LTI tool:
1. Verify the tool is enabled in **Site administration → Plugins → Activity modules → External tool → Manage tools**
2. Check that the tool was created successfully in the LTI Management page
3. Ensure teachers have the necessary permissions to add external tools

### Content Selection Not Working
If the content selection button doesn't work:
1. Verify the LTI tool is properly configured
2. Check browser console for JavaScript errors
3. Ensure pop-ups are not blocked in the browser
4. Verify the Skill5 API is accessible from your network

## Plugin Structure

```
local/skill5/
├── classes/
│   ├── api_manager.php       # Handles Skill5 API communication
│   └── lti_manager.php        # Manages LTI 1.3 tool creation
├── db/
│   └── install.php            # Post-installation hooks
├── lang/
│   └── en/
│       └── local_skill5.php   # English language strings
├── pages/
│   ├── connection_assistant.php  # Connection setup page
│   ├── landing.php               # Initial landing page
│   ├── lti_management.php        # LTI tool management
│   └── user_management.php       # User management page
├── connect.php                # Connection processing script
├── lib.php                    # Core library functions
├── settings.php               # Plugin settings definition
├── user_details.php           # User details API endpoint
├── version.php                # Plugin version information
├── LICENSE.txt                # GNU GPL v3 license
└── README.md                  # This file
```

## Theme Support

This plugin is developed and tested on Moodle Core's **Boost** theme. It should also work with Boost child themes, including Moodle Core's **Classic** theme.

## Moodle Release Support

Due to limited resources, this plugin is maintained for:
- The most recent major release of Moodle
- The most recent LTS release of Moodle

Currently supported versions:
- Moodle 4.1+ (LTS)
- Moodle 4.5+

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.

## Copyright

Copyright (C) 2025 Skill5

## Support

For bug reports, feature requests, or support:
- Check the documentation above
- Review Moodle logs for error details
- Contact your Skill5 account representative
- Consult Moodle community forums for general Moodle/LTI questions

## Changelog

### Version 1.0 (2025-11-11)
- Initial release
- LTI 1.3 integration with Skill5
- Automatic tool configuration
- Connection assistant
- User management interface
- Support for Moodle 4.1+
