<?php

class CRM_Googlegroups_Utils {

  const 
    SETTING_NAME = 'googlegroups_settings';

  static function getSettings() {
    return civicrm_api3('Setting', 'getvalue', ['name' => 'googlegroups_settings']);
  }

  static function setSettings($params = [], $createNew = FALSE) {
    if (!$createNew) {
      $result = civicrm_api3('Setting', 'getvalue', ['name' => 'googlegroups_settings']);
    }
    $result = empty($result) ? [] : $result;
    $params = array_merge($result, $params);
    civicrm_api3('Setting', 'create', ['googlegroups_settings' => $params]);
    return self::getSettings();
  }

  static function setAccessToken($token) {
    return self::setSettings(['access_token' => $token]);
  }
  
  static function getAccessToken() {
    $params = self::getSettings();
    return CRM_Utils_Array::value('access_token', $params);
  }

  static function getStats() {
    $params = self::getSettings();
    return CRM_Utils_Array::value('stats', $params);
  }

  static function setStats($params) {
    return self::setSettings(['stats' => $params]);
  }

  static function getDomains() {
    $params = self::getSettings();
    return CRM_Utils_Array::value('domains', $params, []);
  }

  static function getClient($initTokenIfRequired = TRUE) {
    $params = self::getSettings();

    require_once 'vendor/autoload.php';
    $client = new Google_Client();
    $client->setClientId($params['client_id']);
    $client->setClientSecret($params['client_secret']);
    $client->setApplicationName('CiviCRM GoogleGroups Extension');
    $client->setAccessType('offline');
    $client->addScope(Google_Service_Directory::ADMIN_DIRECTORY_GROUP);
    // Using "consent" ensures that your application always receives a refresh token.
    // If you are not using offline access, you can omit this.
    // This is not really working.
    // $client->setApprovalPrompt("select_account consent");

    $redirectUrl = CRM_Utils_System::url('civicrm/googlegroups/settings', 'reset=1',  TRUE, NULL, FALSE, TRUE, TRUE);
    $client->setRedirectUri($redirectUrl);

    if ($initTokenIfRequired) {
      $accessToken = self::getAccessToken();
      if (!empty($accessToken)) {
        $client->fetchAccessTokenWithRefreshToken($accessToken);
      }
      // If there is no previous token or it's expired.
      if ($client->isAccessTokenExpired()) {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
        exit();
      }
    }
    return $client;
  }

  static function getGroupsToSync($groupIDs = array(), $gc_group_id = null) {
    $params = $groups = array();
    if (!empty($groupIDs)) {
      $groupIDs = implode(',', $groupIDs);
      $whereClause = "entity_id IN ($groupIDs)";
    } else {
      $whereClause = "gc_group_id IS NOT NULL AND gc_group_id <> ''";
    }

    if ($gc_group_id) {
      $whereClause .= " AND gc_group_id = %1 ";
      $params[1] = array($gc_group_id, 'String');
    }
    $query  = "
      SELECT  entity_id, gc_group_id, cg.title as civigroup_title, cg.saved_search_id, cg.children
      FROM    civicrm_value_googlegroup_settings mcs
      INNER JOIN civicrm_group cg ON mcs.entity_id = cg.id
      WHERE $whereClause";
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    while ($dao->fetch()) {
      $groups[$dao->entity_id] =
        array(
          'group_id'              => $dao->gc_group_id,
          'civigroup_title'       => $dao->civigroup_title,
          'civigroup_uses_cache'  => (bool) (($dao->saved_search_id > 0) || (bool) $dao->children),
        );
    }
    return $groups;
  }
  
  static function getGroupContactObject($groupID, $start=null) {
    $group           = new CRM_Contact_DAO_Group();
    $group->id       = $groupID;
    $group->find();

    if($group->fetch()){
      //Check smart groups (including parent groups, which function as smart groups).
      if($group->saved_search_id || $group->children){
        $groupContactCache = new CRM_Contact_BAO_GroupContactCache();
        $groupContactCache->group_id = $groupID;
        if ($start !== null) {
          $groupContactCache->limit($start, CRM_Googlegroups_Form_Sync::BATCH_COUNT);
        }
        $groupContactCache->find();
        return $groupContactCache;
      }
      else {
        $groupContact = new CRM_Contact_BAO_GroupContact();
        $groupContact->group_id = $groupID;
        $groupContact->whereAdd("status = 'Added'");
        if ($start !== null) {
          $groupContact->limit($start, CRM_Googlegroups_Form_Sync::BATCH_COUNT);
        }
        $groupContact->find();
        return $groupContact;
      }
    }
    return FALSE;
  }
  
}
