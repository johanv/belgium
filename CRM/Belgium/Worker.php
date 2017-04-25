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

/**
 * Class CRM_Belgium_Worker - This class performs the actual work.
 */
class CRM_Belgium_Worker {
  /**
   * Updates state_province_id of a Belgian address based on a postal code.
   *
   * @param int $addressId
   * @param int $postalCode
   * @return int|null The guessed state_province_id.
   *
   * Postal code could be inferred from $addressId, but because we know it
   * in advance, we can do all logic in one chained call.
   */
  public function updateProvince($addressId, $postalCode) {
    is_numeric($addressId) or die('$addressId should be numerical.');
    $stateProvinceId = CRM_Belgium_Logic::guessProvince($postalCode);
    $result = civicrm_api3('Address', 'get', [
      'id' => $addressId,
      // Belgium
      'country_id' => 1020,
      'api.Address.create' => [
        'id' => '$value.id',
        'state_province_id' => $stateProvinceId,
      ],
    ]);
    return $result['count'] == 0 ? NULL : $stateProvinceId;
  }

  /**
   * If the contact of the address has no preferred language, guess.
   *
   * @param int $addressId
   * @param int $postalCode
   *
   * Again, we could look up $postalCode, but this saves an API call.
   * FIXME: Inconsistency: This function doesn't overwrite, updateProvince does.
   */
  public function updatePreferredLanguage($addressId, $postalCode) {
    is_numeric($addressId) or die('$addressId should be numerical.');
    is_numeric($postalCode) or die('$postalCode should be numerical.');
    $stateProvinceId = CRM_Belgium_Logic::guessProvince($postalCode);
    if (empty($stateProvinceId)) {
      return;
    }
    $nl = [1785, 1789, 1792, 1793, 1794];
    $fr = [1786, 1787, 1788, 1790, 1791];
    $lang = NULL;
    if (in_array($stateProvinceId, $nl)) {
      // This should actually be nl_BE, but that doesn't seem to exist in
      // CiviCRM.
      $lang = 'nl_NL';
    }
    else if (in_array($stateProvinceId, $fr)) {
      // The same is true for fr_BE.
      $lang = 'fr_FR';
    }
    if (!empty($lang)) {
      // Only change preferred language if it isn't already set.
      civicrm_api3('Address', 'get', [
        'id' => $addressId,
        'api.Contact.get' => [
          'id' => '$value.contact_id',
          'preferred_language' => ['IS NOT NULL' => 1],
          'api.Contact.create' => [
            'id' => '$value.id',
            'preferred_language' => $lang,
          ],
        ],
      ]);
    }
  }

  /**
   * Create database tables for this extension.
   */
  public function createTables() {
    $sql = 'CREATE TABLE IF NOT EXISTS belgium_postal_code(
        postal_code INTEGER NOT NULL PRIMARY KEY,
        location VARCHAR(64) NOT NULL,
        municipality VARCHAR(64) NOT NULL,
        state_province_id INTEGER,
        preferred_language VARCHAR(5)
      )';
    CRM_Core_DAO::executeQuery($sql);
  }

  /**
   * Drops database tables for this extension.
   */
  public function dropTables() {
    $sql = "DROP TABLE belgium_postal_code";
    CRM_Core_DAO::executeQuery($sql);
  }
}