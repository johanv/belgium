# cards.iwwa.belgium

Extension with Belgian magic.

With this extension enabled, if you create or update a Belgian address, the
province is determined based on the postal code. If the address belongs to
a contact that doesn't have a preferred language, a preferred language is
set based on the province. (This mainly applies when a contact was created
using the API, otherwise CiviCRM will use the default language from the
settings, I presume.)

This determination is not 100% correct, but I accept pull requests, as always.
