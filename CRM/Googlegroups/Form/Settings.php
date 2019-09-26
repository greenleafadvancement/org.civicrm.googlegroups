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
      $client = GG::getClient();
      $result = $client->fetchAccessTokenWithAuthCode($code);
      $client->setAccessToken($result);

      if (array_key_exists('error', $result)) {
        CRM_Core_Session::setStatus(ts('Could not authorize connection with google. Make sure credentials are correct.'), ts('Something Went Wrong!'), 'error');
      } else {
        GG::setAccessToken($client->getAccessToken());
        CRM_Core_Session::setStatus(ts('Connection with Google was successfully authorized.'), ts('Success'), 'success');
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

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => !empty($accessToken) ? E::ts('Re-Authorize') : E::ts('Authorize'),
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
    GG::setSettings($params, TRUE);

    GG::initToken();
    parent::postProcess();
  }

}
