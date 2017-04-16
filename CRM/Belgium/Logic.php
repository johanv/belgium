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
 * Some database-independent logic.
 */
class CRM_Belgium_Logic {
  /**
   * Returns the state_province_id that probably corresponds with the postal code.
   * @param int $postalCode
   * @return int state_province_id
   *
   * TODO: Use constants instead of magic numbers for provinces.
   */
  public static function guessProvince($postalCode) {
    is_numeric($postalCode) || die('$postalCode should be numerical.');
    if ($postalCode < 1300) {
      // Brussels Hoofdstedelijk Gewest
      $stateProvinceId = 5217;
    }
    else if ($postalCode < 1500) {
      // Brabant Wallon
      $stateProvinceId = 1786;
    }
    else if ($postalCode < 2000) {
      // Vlaams Brabant, part 1
      $stateProvinceId = 1793;
    }
    else if ($postalCode < 3000) {
      // Antwerpen
      $stateProvinceId = 1785;
    }
    else if ($postalCode < 3500) {
      // Vlaams Brabant, part 2
      $stateProvinceId = 1973;
    }
    else if ($postalCode < 4000) {
      // Limburg
      $stateProvinceId = 1789;
    }
    else if ($postalCode < 5000) {
      // Liège
      $stateProvinceId = 1788;
    }
    else if ($postalCode < 6000) {
      // Namur
      $stateProvinceId = 1791;
    }
    else if ($postalCode < 6600) {
      // Hainaut, part 1
      $stateProvinceId = 1787;
    }
    else if ($postalCode < 7000) {
      // Luxembourg
      $stateProvinceId = 1790;
    }
    else if ($postalCode < 8000) {
      // Hainaut, part 2
      $stateProvinceId = 1787;
    }
    else if ($postalCode < 9000) {
      // West-Vlaanderen
      $stateProvinceId = 1794;
    }
    else {
      // Oost-Vlaanderen
      $stateProvinceId = 1792;
    }
    return $stateProvinceId;
  }

}