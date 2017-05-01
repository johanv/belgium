# cards.iwwa.belgium

Extension with some tools for Belgium

This extension depends on:

* [org.civicoop.configitems](https://github.com/CiviCooP/org.civicoop.configitems)
  to configure the available currencies. This may be overkill.
* [be.chiro.civi.queryapitools](https://github.com/Chirojeugd-Vlaanderen/queryapitools)
  to create the `PostalCode` API. This is a temporary hack, it would be better to
  [create a proper entity](https://docs.civicrm.org/dev/en/master/extensions/civix/#generate-entity).
  You can choose not to install queryapitools, in that case you can't use the
  `PostalCode` api.
  
It offers the following goodies:

## Automatically assign province and preferred language

With this extension enabled, if you create or update a Belgian address, the
province is determined based on the postal code. If the address belongs to
a contact that doesn't have a preferred language, a preferred language is
set based on the province. (This mainly applies when a contact was created
using the API, otherwise CiviCRM will use the default language from the
settings, I presume.)

This determination is not 100% correct, but I accept pull requests, as always.

## Some typical Belgian configuration

This extension changes some settings of your CiviCRM extension. The idea is
to apply some sensible settings for Belgian CiviCRM instances:

* date format (d/m/y)
* currency (EUR)
* default country (Belgium)
* ; as field separator (to please Belgian Excel, argh)
* address format and mailing format

The extension also changes the default language to nl_NL, but this change
is only useful for the Dutch speaking part of Belgium. This is still a bug, there
should be a way to choose a language (Dutch, French, German, English). Maybe using a
setting.

## PostalCode.get API

The extension provides a nice API: `PostalCode.get`. Some examples using drush:

```
drush cvapi PostalCode.get postal_code=2240
drush cvapi PostalCode.get municipality=Zandhoven
```

This will only work if you install the
[be.chiro.civi.queryapitools](https://github.com/Chirojeugd-Vlaanderen/queryapitools)
extension as well (v.1.1 or later).

## belgium_postal_code table

The extension adds a table `belgium_postal_code` to the database that might be
useful for your custom searches and reports.