# org.civicrm.googlegroups

![Screenshot](/images/screenshot.png)

The extension uses Google's directory API to sync Google Groups member list with that of contacts in CiviCRM Groups.
The extension supports latest version of CiviCRM ver 5.x and based on Google Client Library v2.x.

Features:
* Syncs the contacts from multiple CiviCRM groups (regular or smart) to specified Google Groups.
* Adds a scheduled job to automatically sync once per day.
* Requires only one time Authorization, and works based on long lived Refresh Token.
* Supports multiple domains within a Google Apps system.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.6+
* CiviCRM v5.0+
* A G Suite domain with API access enabled.
* A Google account in that domain with administrator privileges.

## Installation (Web UI)

TODO:

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl org.civicrm.googlegroups@https://github.com/FIXME/org.civicrm.googlegroups/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/org.civicrm.googlegroups.git
cv en googlegroups
```

## Usage

* Enable the API access from the Admin console in order to make requests to the Directory API.
To enable the API, log in to your admin account and select Security. If you do not see Security listed, select More controls and then Security from the options shown in the gray box. Select API reference, and then select the checkbox to Enable API access. Save your changes. 
* Set up a new project in the Google APIs Console and activate Admin SDK service for this project. See the Google APIs Console Help in the upper right corner of the Console page for more information about creating your API project. 
* Create authorization credentials. Open the Credentials page in the API Console. Click Create credentials > OAuth client ID. Complete the form. Set the application type to Web application.
* Identify access scopes. 

(TODO - Extension Usage)

## Known Issues

Sync is only one way - from CiviCRM to GoogleGroups.

## Credits
