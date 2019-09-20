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
        CRM_Core_Session::setStatus(ts('Unable to establish connection with google groups. Make sure credentials are correct.'), ts('Something Went Wrong!'), 'error');
      } else {
        $params = ['access_token' => $result['access_token']];
        GG::setSettings($params);
        CRM_Core_Session::setStatus(ts('Connection with Google Groups was successfully established'), ts('Success'), 'success');
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
    $accessToken = GG::getAccessToken();

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => $accessToken ? E::ts('Reestablish connection with Google Groups') : E::ts('Establish connection with Google Groups'),
        'isDefault' => TRUE,
      ),
    ));
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    $defaults = GG::getSettings();
    return $defaults;
  }

  public function postProcess() {
    $values = $this->exportValues();

    // fixme: sanitize
    $params = [
      'client_id' => $values['client_id'],
      'client_secret' => $values['client_secret'],
    ];
    GG::setSettings($params, TRUE);

    GG::initToken();
    parent::postProcess();
  }

}
