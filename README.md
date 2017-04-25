# cards.iwwa.belgium

Extension with some tools for Belgium

## Automatically assign province and preferred language

With this extension enabled, if you create or update a Belgian address, the
province is determined based on the postal code. If the address belongs to
a contact that doesn't have a preferred language, a preferred language is
set based on the province. (This mainly applies when a contact was created
using the API, otherwise CiviCRM will use the default language from the
settings, I presume.)

This determination is not 100% correct, but I accept pull requests, as always.

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