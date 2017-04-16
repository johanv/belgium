<?php
/*
  cards.iwwa.belgium - Useful features for Belgium
  Copyright (C) 2017  Johan Vervloet

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
    is_numeric($addressId) || die('$addressId should be numerical.');
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
   * @param int|null $streetProvinceId
   *
   * Again, we could look up $streetProvinceId, but this saves an API call.
   */
  public function updatePreferredLanguage($addressId, $streetProvinceId) {
    is_numeric($addressId) || die('$addressId should be numerical.');
    if (empty($streetProvinceId)) {
      return;
    }
    is_numeric($streetProvinceId) || die('$postalCode should be numerical.');
    $nl = [1785, 1789, 1792, 1793, 1794];
    $fr = [1786, 1787, 1788, 1790, 1791];
    $lang = NULL;
    if (in_array($streetProvinceId, $nl)) {
      // This should actually be nl_BE, but that doesn't seem to exist in
      // CiviCRM.
      $lang = 'nl_NL';
    }
    else if (in_array($streetProvinceId, $fr)) {
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
}