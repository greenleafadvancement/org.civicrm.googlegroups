<?php

require_once 'googlegroups.civix.php';
use CRM_Googlegroups_ExtensionUtil as E;
use CRM_Googlegroups_Utils as GG;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function googlegroups_civicrm_config(&$config) {
  _googlegroups_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function googlegroups_civicrm_install() {
  $extensionDir       = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
  $customDataXMLFile  = $extensionDir  . '/xml/custom.xml';
  $import = new CRM_Utils_Migrate_Import();
  $import->run($customDataXMLFile);

  $params = array(
    'sequential'    => 1,
    'name'          => 'Google Groups Sync',
    'description'   => 'Sync contacts between CiviCRM and Google Groups',
    'run_frequency' => 'Daily',
    'api_entity'    => 'Googlegroups',
    'api_action'    => 'sync',
    'is_active'     => 0,
  );
  $result = civicrm_api3('job', 'create', $params);
  _googlegroups_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function googlegroups_civicrm_enable() {
  _googlegroups_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *

 // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function googlegroups_civicrm_navigationMenu(&$menu) {
  _googlegroups_civix_insert_navigation_menu($menu, 'Administer/System Settings', [
    'label' => E::ts('Google Groups Settings'),
    'name' => 'googlegroups_settings',
    'url' => CRM_Utils_System::url('civicrm/googlegroups/settings', 'reset=1'),
    'permission' => 'administer CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
  ]);
  _googlegroups_civix_insert_navigation_menu($menu, 'Contacts', [
    'label' => E::ts('Google Groups Settings'),
    'name' => 'googlegroups_settings',
    'url' => CRM_Utils_System::url('civicrm/googlegroups/settings', 'reset=1'),
    'permission' => 'administer CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
  ]);
  _googlegroups_civix_insert_navigation_menu($menu, 'Contacts', [
    'label' => E::ts('Google Groups Sync'),
    'name' => 'googlegroups_sync',
    'url' => CRM_Utils_System::url('civicrm/googlegroups/sync', 'reset=1'),
    'permission' => 'administer CiviCRM',
    'operator' => 'OR',
    'separator' => 1,
  ]);
  _googlegroups_civix_navigationMenu($menu);
}

function googlegroups_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Group_Form_Edit' && (
    $form->getAction() == CRM_Core_Action::ADD || 
    $form->getAction() == CRM_Core_Action::UPDATE
  )) {
    $lists = [];
    $lists = civicrm_api3('Googlegroups', 'getgroups', []);
    if (!empty($lists['values'])) {
      $form->add('select', 'googlegroup', ts('Google Group'), array('' => '- select -') + $lists['values'], FALSE );
      $templatePath = realpath(dirname(__FILE__)."/templates/CRM/Googlegroups/Form");
      CRM_Core_Region::instance('page-body')->add(array('template' => "{$templatePath}/Settings.extra.tpl"));

      $groupId = $form->getVar('_id');
      if (($form->getAction() == CRM_Core_Action::UPDATE) && !empty($groupId)) {
        $defaults = [];
        $ggDetails = GG::getGroupsToSync([$groupId]);
        if (!empty($ggDetails)) {
          $defaults['googlegroup'] = $ggDetails[$groupId]['group_id'];
        }
        $form->setDefaults($defaults);  
      }
    }
  }
}
