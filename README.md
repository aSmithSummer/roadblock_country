# Roadblocks Country Silverstripe module

This module extends the Roadblocks module, providing configurable country identification.

The country IP ranges can be obtained from many third party providers, but the IP address from and to needs converting to a number before setting up.
For example 0.0.0.0 is the number 0, while 255.255.255.255 is 4,294,967,296 (256 x 256 x 256 x 256)

## ModelAdmin
New model admin to administer country ip range data.
Bulk upload has ability to remove existing data for batch uploading.

## Logs
The country name is added to the request for each request. This way you can add classifications such as 'outside nz' to any IP range not in NZ.

## Rules
New rule evaluation to test if a country name matches that on the request.

## Installation

```sh
composer require aSmithSummer/roadblock_country
```
