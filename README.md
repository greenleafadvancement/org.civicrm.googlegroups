# org.civicrm.googlegroups

<img alt="Screenshot" src="/images/civi-sync-stats.png" width="50%"/>

Google Groups
![Screenshot](/images/google-groups.png)

The extension uses Google's directory API to sync Google Groups member list with that of contacts in CiviCRM Groups.
The extension supports latest version of CiviCRM ver 5.x and based on Google Client Library v2.x.

Features:
* Syncs the contacts from multiple CiviCRM groups (regular or smart) to specified Google Groups.
* Adds a scheduled job to automatically sync once per day.
* Requires only one time Authorization, and works based on long lived Refresh Token.
* Supports multiple domains within a Google Apps system.
* An email other than home/work could be used for the sync. Email with location type "Google" is given preference over primary email.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.6+
* CiviCRM v5.0+
* A G Suite domain with API access enabled.
* A Google account in that domain with administrator privileges.

## Installation (Web UI)

TODO: *this extension is currently not available to install through the web ui*

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl org.civicrm.googlegroups@https://github.com/greenleafadvancement/org.civicrm.googlegroups/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/greenleafadvancement/org.civicrm.googlegroups.git
cv en googlegroups
```

## G Suite Setup: Enabling API, Creating Project, OAuth and Consent

* Before you can begin, you need to create a new project in the [Google APIs Console](https://code.google.com/apis/console).
![Screenshot](/images/new-project.png)

* Enable the API access from Google Cloud Console in order to make requests to the Directory API. To enable the API, At the top-left, click Menu menu > APIs & Services > Library, and enable “Admin SDK API”.
<img alt="Screenshot" src="/images/enable-api-1.png" width="50%"/>
<img alt="Screenshot" src="/images/enable-api-2.png" width="50%"/>
The enabled api should now be visible in "Enabled APIs and Services" section.
<img alt="Screenshot" src="/images/enable-api-3.png" width="80%"/>

* Create OAuth consent screen.
![Screenshot](/images/oauth-consent-1.png)
![Screenshot](/images/oauth-consent-2.png)

* Create authorization credentials. Open the Credentials section from "APIs & Services". Click Create credentials > OAuth client ID.
![Screenshot](/images/create-credentials-1.png)
![Screenshot](/images/create-credentials-2.png)
![Screenshot](/images/create-credentials-3.png)
![Screenshot](/images/credentials-display.png)

## Extension Setup

* Goto Administer >> System Settings >> Google Groups Settings.

* Take a note of the url. This should match with redirect uri setup in OAuth consent screen earlier. If it does not match, edit your oauth consent screen again to correct it.

* Enter client id and secret from the credentials created earlier.

* Enter GSuite domain. For multiple domains enter comma separated domains.

* Save would redirect to google authorization (OAuth) prompt. Once authorized it should redirect back to extension's Google Groups Settings screen. If it does not, either:
A. Redirect uri has not been setup correctly OR
B. Your setup is a local one and not accessible to public.

* Once setup, CiviCRM group add or edit screen would render a Google Group dropdown. Select a matching google group. Sync process will make sure that members of google groups are synced from that of matching Civi Group.
<img width="761" alt="image" src="https://github.com/greenleafadvancement/org.civicrm.googlegroups/assets/3448551/3563c23c-e389-4572-ab40-433ecb703a52">

* Use Contacts >> Google Groups Sync to run the sync process manually.

* To automate the sync, enable the 'Google Groups Sync (Daily)' job on Administer >> System Settings >> Scheduled Jobs screen.

![Screenshot](/images/civi-settings.png)

## Known Issues

Sync is only one way - from CiviCRM to GoogleGroups.

## Credits

* Original extension (uk.co.vedaconsulting.googlegroup) development by Veda NFP Consulting Ltd (https://vedaconsulting.co.uk/) supporting CiviCRM versions upto v4.6.
* Rewrite for CiviCRM v5.x funded by Greenleaf Advancement (https://greenleafadvancement.com) and Joshua Gowans (https://civicrm.org), developed by Deepak Srivastava (https://mountev.co.uk).
