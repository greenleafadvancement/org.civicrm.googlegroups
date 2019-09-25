<?php

use CRM_Googlegroups_Utils as GG;

/**
 * Googlegroups API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
 
/**
 * Googlegroups Get Googlegroups Groups API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */ 
function civicrm_api3_googlegroups_getgroups($params) {
  $groups = [];
  $client = GG::getClient();
  if (!$client->isAccessTokenExpired()) {
    $results = [];
    $service = new Google_Service_Directory($client);
    $domains = GG::getDomains();
    if (!empty($domains)) {
      foreach ($domains as $domain) {
        try {
          $pageToken = "";
          do {
            $optParams = array('domain' => trim($domain), 'pageToken' => $pageToken);
            $results = $service->groups->listGroups($optParams);
            foreach($results->getGroups() as $result) {
              $groups[$result['id']] = "{$domain}:{$result['name']}::{$result['email']}";
            }
            $pageToken = $results->nextPageToken;
          } while($pageToken);
        } 
        catch (Exception $e) {
          $errors = $e->getErrors();
          //if ($errors[0]['message'] == 'Login Required') {
          //}
          return [];
        }
      }
    }
  }
  return civicrm_api3_create_success($groups);
}

function civicrm_api3_googlegroups_getmembers($params) {
  $members = [];
  $client = GG::getClient();
  if (!$client->isAccessTokenExpired()) {
    $service = new Google_Service_Directory($client);
    try {
      $pageToken = "";
      do {
        $optParams = array('pageToken' => $pageToken);
        $results = $service->members->listMembers($params['group_id'], $optParams);
        foreach($results->getMembers() as $result) {
          $members[$result['id']] = $result['email'];
        }
        $pageToken = $results->nextPageToken;
      } while($pageToken);
    } 
    catch (Exception $e) {
      return [];
    }
  }
  return civicrm_api3_create_success($members);
}

function _civicrm_api3_googlegroups_getmembers_spec(&$params) {
  $params['group_id']['api.required'] = 1;
}

function civicrm_api3_googlegroups_deletemember($params) {
  $members = [];
  $client = GG::getClient();
  if (!$client->isAccessTokenExpired()) {
    $client->setUseBatch(TRUE);
    $batch = new Google_Http_Batch($client);
    $service = new Google_Service_Directory($client);
    try {
      foreach ($params['member'] as $key => $member) {
        $batch->add($service->members->delete($params['group_id'], $member));
      }
      $response = $batch->execute();
    } 
    catch (Exception $e) {
      return [];
    }
  }
  return civicrm_api3_create_success($members);
}

function _civicrm_api3_googlegroups_deletemember_spec(&$params) {
  $params['group_id']['api.required'] = 1;
}

function civicrm_api3_googlegroups_subscribe($params) {
  $results = [];
  $client = GG::getClient();
  if (!$client->isAccessTokenExpired()) {
    $client->setUseBatch(TRUE);
    $batch = new Google_Http_Batch($client);
    $service = new Google_Service_Directory($client);
    try {
      foreach ($params['emails'] as $email) {
        $member = new Google_Service_Directory_Member();
        $member->setEmail($email);
        $member->setRole($params['role']);
        $res = $batch->add($service->members->insert($params['group_id'], $member));
      }
      $response = $batch->execute();
    } 
    catch (Exception $e) {
      return [];
    }
  }
  return civicrm_api3_create_success($results);
}

function _civicrm_api3_googlegroups_subscribe_spec(&$params) {
  $params['group_id']['api.required'] = 1;
}

function civicrm_api3_googlegroups_sync($params) {
  $result = [];
	// Do push from CiviCRM to Google Group 
  $runner = CRM_Googlegroups_Form_Sync::getRunner($skipEndUrl = TRUE);
  if ($runner) {
    $result = $runner->runAll();
  }

  if ($result['is_error'] == 0) {
    return civicrm_api3_create_success();
  }
  else {
    return civicrm_api3_create_error();
  }
}
