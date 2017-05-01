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
 * Collection of upgrade steps.
 */
class CRM_Belgium_Upgrader extends CRM_Belgium_Upgrader_Base {
  private $worker;

  public function __construct($extensionName, $extensionDir) {
    parent::__construct($extensionName, $extensionDir);
    // I would like to inject this, but I don't know how to do DI with
    // CiviCRM.
    $this->worker = new CRM_Belgium_Worker();
  }

  public function install() {
    $this->worker->createTables();
    $this->worker->importData();
  }

  public function uninstall() {
    $this->worker->dropTables();
  }

  /**
   * There were no tables in version 1.0, so let's create them.
   */
  public function upgrade_4701() {
    // createTables won't do anything if the tables already exist.
    $this->worker->createTables();
    return $this->worker->importData();
  }

  /**
   * Belgian settings (#4).
   */
  public function upgrade_4702() {
    civicrm_api3('Setting', 'create', [
      // Date format
      'dateformatDatetime' => '%E %B %Y %H:%M',
      'dateformatFull' => '%E %B %Y',
      'dateformatPartial' => '%B %Y',
      'dateformatFinancialBatch' => '%d/%m/%Y',
      'dateformatshortdate' => '%d/%m/%Y',
      'dateInputFormat' => 'dd/mm/yy',
      // Currencies
      'defaultCurrency' => 'EUR',
      'monetaryThousandSeparator' => '',
      // Default country
      'defaultContactCountry' => 1020,
      // For the Belgian Excel god:
      'fieldSeparator' => ';',
      // Language
      'lcMessages' => 'nl_NL',
      // Currency settings
      'moneyformat' => '%c %a',
      'moneyvalueformat' => '%!i',
      // Label and adress format
      'address_format' => "{contact.address_name}\n{contact.supplemental_address_1}\n{contact.street_address}\n{contact.supplemental_address_2}\n{contact.postal_code}{ }{contact.city}\n{contact.country}",
      'mailing_format' => "{contact.addressee}\n{contact.supplemental_address_1}\n{contact.street_address}\n{contact.supplemental_address_2}\n{contact.postal_code}{ }{contact.city}\n{contact.country}",
    ]);

    // Apply configuration as defined in the json files in the resources folder:
    civicrm_api3('Civiconfig', 'load_json', [
      'path' => realpath(__DIR__ . '/../../') . '/resources/'
    ]);
    return TRUE;
  }
}
