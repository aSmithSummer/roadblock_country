# Roadblocks Country Silverstripe module

This module extends the Roadblocks module, providing configurable country identification.

The country IP ranges can be obtained from many third party providers, but the IP address from and to needs converting to a number before setting up.
For example 0.0.0.0 is the number 0, while 255.255.255.255 is 4,294,967,296 (256 x 256 x 256 x 256)

## Logs
The country name is added to the request for each request. This way you can add classifications such as 'outside nz' to any IP range not in NZ.

## Rules

Roadblock rules gain four new fields:
- Country:- the text representation of the country loaded
- Country allowed:- checkbox that reverses the log, ie allow vs deny
- Country number:- the number of requests with the country present
- Country offset:- the time in seconds to look back over requests

## Model Admin

A new country ip ranges model admin to manage the country ip ranges. This has bulk import and export functionality.

## Test 'inspectors'

The roadblock rule inspectors model admin tab can specify the name of a country.
// TODO add country ip to the request log test tab.

## Installation

```sh
composer require asmithsummer/roadblock_country
```

## Example configuration

As we are setting a new session for un-authenticated members, to prevent new sessions being created when they log in you should set login_recording to true. This is not fool proof but a big improvement.

in your app's base _config add the following:

```yaml
---
Name: app_roadblock_country_settings
After: roadblock_country_settings
---
aSmithSummer\RoadblockCountry\Model\CountryIPRange:
  default_country_name: 'Outside New Zealand'
```

## License

See [License](LICENSE.md)

This module template defaults to using the "BSD-3-Clause" license. The BSD-3 license is one of the most
permissive open-source license and is used by most Silverstripe CMS module.
