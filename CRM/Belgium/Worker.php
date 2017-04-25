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
    $stateProvinceId = CRM_Belgium_Logic::getProvince($postalCode);
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
    $lang = CRM_Belgium_Logic::getLanguage($postalCode);
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
    // TODO: make table name configurable.
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

  /**
   * Imports the data from the csv file.
   *
   * @return TRUE if import succeeded.
   */
  public function importData() {
    $file = realpath(__DIR__ . '/../../') . '/data_sources/postal_codes.csv';
    $handle = fopen($file, 'r');
    if (!$handle) {
      return FALSE;
    }
    while (($line = fgets($handle)) !== FALSE) {
      $fields = explode(',', $line);
      if (!is_numeric($fields[0])) {
        // skip invalid lines.
        continue;
      }
      // First delete existing postal code, so that running the import twice
      // is not a problem.
      $sql = 'DELETE FROM belgium_postal_code WHERE postal_code = %1';
      $params = [1 => [$fields[0], 'Integer']];
      CRM_Core_DAO::executeQuery($sql, $params);

      // CRM_Core_DAO doesn't seem to handle NULL very well. So convert NULL
      // to the empty string.
      $provinceId = CRM_Belgium_Logic::getProvince($fields[0]);
      if ($provinceId == NULL) {
        $provinceId = '';
      }
      $lang = CRM_Belgium_Logic::getLanguage($fields[0]);
      if ($lang == NULL) {
        $lang = '';
      }

      $sql = '
          INSERT INTO belgium_postal_code(postal_code, location, municipality, state_province_id, preferred_language)
          VALUES(%1,%2,%3,%4,%5)';
      $params = [
        1 => [intval($fields[0]), 'Integer'],
        2 => [$fields[1], 'String'],
        3 => [$fields[2], 'String'],
        // getProvince returns an integer or null, and it seems that there is
        // no parameter type for nullable integer :-(
        4 => [$provinceId, 'String'],
        5 => [$lang, 'String'],
      ];
      CRM_Core_DAO::executeQuery($sql, $params);
    }
    fclose($handle);
    return TRUE;
  }
}