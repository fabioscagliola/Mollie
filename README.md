Enrol CoursePayment
====================
![MFreak.nl](http://MFreak.nl/logo_small.png)

* Author: Luuk Verhoeven, [Mfreak.nl](http://Mfreak.nl)
* Requires at least: Moodle 3.5
* License: [Avetica](http://avetica.nl)
* Supports PHP: 7.2

![Moodle35](https://img.shields.io/badge/moodle-3.5-brightgreen.svg)
![Moodle36](https://img.shields.io/badge/moodle-3.6-brightgreen.svg)
![Moodle37](https://img.shields.io/badge/moodle-3.7-brightgreen.svg)
![Moodle38](https://img.shields.io/badge/moodle-3.8-brightgreen.svg)
![Moodle39](https://img.shields.io/badge/moodle-3.9-brightgreen.svg)
![Moodle310](https://img.shields.io/badge/moodle-3.10-brightgreen.svg)
![Moodle311](https://img.shields.io/badge/moodle-3.11-brightgreen.svg)
![PHP7.2](https://img.shields.io/badge/PHP-7.2-brightgreen.svg)
![PHP7.4](https://img.shields.io/badge/PHP-7.4-brightgreen.svg)

With this plugin you can sell courses to your moodle users. There is also a [availability plugin](https://deploy01.avetica.net/technisch-team/moodlefreak/availability_coursepayment/) available.

Description
====================
This plugin allows you to sell courses with multiple gateways.

Has support for:
* Mollie gateway.
* IPN/Callbacks.
* Query openen transactions every hour with cron.
* Easy to build own extend with another gateway.
* Instance based settings like currency, enrol period.
* Global notification settings.
* A global sandbox and debug switches.
* After successful transaction user will be enrolled.
* Multiple Mollie accounts support, account selection based on matching profile field value.

Installation
====================

1. copy this plugin to the `enrol` folder called `coursepayment`
2. login as administrator
3. go to Site Administrator > Notification
4. install the plugin
5. register on the gateway page you interested at
6. add global settings and your gateway settings

Changelog
====================

See Git for the complete history, major changes will be listed below
- 2022120100 - Moodle 4.1 / Moodle Workplace 4.0 and PHP 7.4 support implemented.
- 2020103000 - Moodle 3.11 support implemented.
- 2020103000 - Moodle 3.10 support implemented.
- 2020012800 - Moodle 3.9 support implemented, only supports PHP 7.2 and higher.
- 2020012800 - Mollie connect is required for new installations.
- 2020011500 - Upgrade Mollie API to the latest version, using composer/vendor now.
- 2020010200 - Moodle 3.8 support implemented.
- 2019052800 - Moodle 3.7 support implemented.
- 2019052000 - Mollie account claim removed.
- 2018110601 - PDF Invoice generation added.
- 2018070500 - Added privacy provider GDPR.
- 2018070500 - Git folder structure changed.
- 2018010800 - Added support focustom transaction Mollie description.
- 2017082101 - Added multi account option, to support multiple Mollie accounts.
   The correct payment account is selected based on profile field.
- 2017021701 - Added reseller support and direct account create function.
- 2017021000 - Added latest https://github.com/mollie/mollie-api-php
- 2016111200 - Support for availability_coursepayment, purchase a single activity.
- 2015061202 - Intergration of customable vat percentage per instance and global.
- 2015061201 - We added invoice mail support.
