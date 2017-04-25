<?php

/**
 * PostalCode.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_postal_code_Get_spec(&$spec) {
  $spec['postal_code'] = [
    'name' => 'postal_code',
    'title' => ts('Postal code'),
    'type' => CRM_Utils_Type::T_INT,
  ];
  $spec['location'] = [
    'name' => 'location',
    'title' => ts('Location'),
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $spec['municipality'] = [
    'name' => 'municipality',
    'type' => CRM_Utils_Type::T_STRING,
    'title' => ts('Municipality'),
  ];
  $spec['state_province_id'] = [
    'name' => 'state_province_id',
    'title' => ts('Province id'),
    'type' => CRM_Utils_Type::T_INT,
  ];
  $spec['preferred_language'] = [
    'name' => 'preferred_language',
    'type' => CRM_Utils_Type::T_STRING,
    'title' => ts('Preferred language'),
  ];
}

/**
 * PostalCode.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_postal_code_Get($params) {
  // I think a field 'id' is required, so I will just 'recycle' the postal
  // code as id.
  $query = "SELECT * FROM belgium_postal_code";
  $fields = [];
  _civicrm_api3_postal_code_Get_spec($fields);
  $result = CRM_Queryapitools_Tools::BasicGet($query, $params, NULL, $fields);
  return $result;
}

