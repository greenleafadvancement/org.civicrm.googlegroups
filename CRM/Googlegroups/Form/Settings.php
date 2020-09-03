<?php

use CRM_Googlegroups_ExtensionUtil as E;
use CRM_Googlegroups_Utils as GG;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Googlegroups_Form_Settings extends CRM_Core_Form {

  public function preProcess() {
    $code = CRM_Utils_Request::retrieve('code', 'String', $this);
    if ($code) {
      $client = GG::getClient(FALSE);
      // note $result is accessToken, and not refreshToken
      $result = $client->fetchAccessTokenWithAuthCode($code);

      if (array_key_exists('error', $result)) {
        CRM_Core_Session::setStatus(ts('Could not authorize connection with google. Make sure credentials are correct.'), ts('Something Went Wrong!'), 'error');
      } else {
        // Long lived refresh token are sent only first time.
        if ($client->getRefreshToken()) {
          GG::setAccessToken($client->getRefreshToken());
          CRM_Core_Session::setStatus(E::ts('Connection with Google was successfully authorized.'), E::ts('Success'), 'success');
        } else {
          GG::setAccessToken('');
          CRM_Core_Session::setStatus(E::ts('Something wrong. Couldn\'t fetch the refresh token.'), E::ts('Error'), 'error');
        }
      }
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/googlegroups/settings", 'reset=1'));
    }
  }

  public function buildQuickForm() {
    $this->addElement('text', 'client_id', E::ts('Client ID'), array(
      'size' => 98,
    ));
    $this->addElement('text', 'client_secret', E::ts('Client Secret'), array(
      'size' => 48,
    ));
    $this->addElement('text', 'domains', ts('Domain Names'), array(
      'size' => 48,
    ));
    $accessToken = GG::getAccessToken();
    $client = GG::getClient(FALSE);
    if (!empty($accessToken)) {
      $accessResult = $client->fetchAccessTokenWithRefreshToken($accessToken);
    }
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => $client->isAccessTokenExpired() ? E::ts('Click to Authorize') : E::ts('Authorized (Click to Re-Authorize)'),
        'isDefault' => TRUE,
      ),
    ));
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    $defaults = GG::getSettings();
    if (!empty($defaults['domains'])) {
      $defaults['domains'] = implode(',', $defaults['domains']);
    }
    return $defaults;
  }

  public function postProcess() {
    $values = $this->exportValues();

    // fixme: sanitize
    $params = [
      'client_id' => $values['client_id'],
      'client_secret' => $values['client_secret'],
    ];
    if (!empty($values['domains'])) {
      $values['domains'] = explode(',', $values['domains']);
      $params['domains'] = array_map('trim', $values['domains']);
    }
    // Overwrite settings
    GG::setSettings($params);
    // init token if required.
    $client = GG::getClient(TRUE, TRUE);

    if ($client->isAccessTokenExpired()) {
      CRM_Core_Session::setStatus(ts('Authorization failed.'), ts('Error'), 'error');
    } else {
      CRM_Core_Session::setStatus(ts('Access Token Refreshed.'), ts('Success'), 'success');
    }

    parent::postProcess();
  }

}
