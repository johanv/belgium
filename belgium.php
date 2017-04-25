<?php
/*
  cards.iwwa.belgium - Useful features for Belgium
  Copyright (C) 2017  Johan Vervloet
  Issues #1, #2 Copyright (C) 2017  Chirojeugd-Vlaanderen vzw

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'belgium.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function belgium_civicrm_config(&$config) {
  _belgium_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function belgium_civicrm_xmlMenu(&$files) {
  _belgium_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function belgium_civicrm_install() {
  _belgium_civix_civicrm_install();
}

/**
* Implements hook_civicrm_postInstall().
*
* @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
*/
function belgium_civicrm_postInstall() {
  _belgium_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function belgium_civicrm_uninstall() {
  _belgium_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function belgium_civicrm_enable() {
  _belgium_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function belgium_civicrm_disable() {
  _belgium_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function belgium_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _belgium_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function belgium_civicrm_managed(&$entities) {
  _belgium_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function belgium_civicrm_caseTypes(&$caseTypes) {
  _belgium_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function belgium_civicrm_angularModules(&$angularModules) {
_belgium_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function belgium_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _belgium_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_post().
 *
 * If province and preferred language autodetection don't work, make sure that
 * the CiviCRM variables `belgium_disable_province_autodetection` and
 * `belgium_disable_language_autodetection` are empty.
 *
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $objectRef
 */
function belgium_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Address' && ($op == 'edit' || $op == 'create')) {
    $worker = new CRM_Belgium_Worker();
    $stateProvinceId = NULL;
    // The 'null' as string is a strange CiviCRM thing in forms. It is a bug
    // by design or something.
    if (!(empty($objectRef->postal_code) || $objectRef->postal_code == 'null')) {
      // A postal code is given.
      if (empty($objectRef->state_province_id) || $objectRef->state_province_id == 'null') {
        // If state_province_id is not explicitly given
        // do an educated guess. (only if the province autodetection is not
        // disabled.)
        empty(Civi::settings()
          ->get('belgium_disable_province_autodetection')) and $worker->updateProvince($objectId, $objectRef->postal_code);
      }
      // Also update preferred language, if it's not already set.
      empty(Civi::Settings()
        ->get('belgium_disable_language_autodetection')) and $worker->updatePreferredLanguage($objectId, $objectRef->postal_code);
    }
  }
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function belgium_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function belgium_civicrm_navigationMenu(&$menu) {
  _belgium_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'cards.iwwa.belgium')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _belgium_civix_navigationMenu($menu);
} // */
