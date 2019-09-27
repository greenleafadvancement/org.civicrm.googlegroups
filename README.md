# org.civicrm.googlegroups

![Screenshot](/images/extension.png)

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

## G Suite Setup: Enabling API, Creating Project, OAuth and Consent 

* Enable the API access from the Admin console in order to make requests to the Directory API. To enable the API, log in to your admin account (at admin.google.com) and select Security. If you do not see Security listed, select More controls and then Security from the options shown in the gray box. 
![Screenshot](/images/admin-console.png)

* Select API reference, and then select the checkbox to Enable API access. Save your changes.
![Screenshot](/images/enable-api.png)

* Set up a new project in the [Google APIs Console](https://code.google.com/apis/console) 
![Screenshot](/images/new-project.png)

* Activate Admin SDK service for the project created. 
![Screenshot](/images/activate-admin-sdk.png)

* Create OAuth consent screen.
![Screenshot](/images/oauth-consent.png)

* Create authorization credentials. Open the Credentials page in the API Console. Click Create credentials > OAuth client ID. 
![Screenshot](/images/create-credentials.png)

* Complete the form. Set the application type to Web application.
![Screenshot](/images/create-oauth-client-id.png)


## Extension Setup

* Goto Administer >> System Settings >> Google Groups Settings.

* Take a note of the url. This should match with redirect uri setup in OAuth consent screen earlier. If it does not match, edit your oauth consent screen again to correct it.

* Enter client id and secret from the credentials created earlier.

* Enter GSuite domain. For multiple domains enter comma separated domains.

* Save would redirect to google authorization (OAuth) prompt. Once authorized it should redirect back to extension's Google Groups Settings screen. If it does not, either: 
A. Redirect uri has not been setup correctly OR 
B. Your setup is a local one and not accessible to public.

* Once setup, CiviCRM group add or edit screen would render a Google Group dropdown. Select a matching google group. Sync process will make sure that members of google groups are synced from that of matching Civi Group.

* Use Contacts >> Google Groups Sync to run the sync process manually.

* To automate the sync, enable the 'Google Groups Sync (Daily)' job on Administer >> System Settings >> Scheduled Jobs screen.


## Known Issues

Sync is only one way - from CiviCRM to GoogleGroups.

## Credits
