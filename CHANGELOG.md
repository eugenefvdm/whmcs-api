# Changelog

All notable changes to `whmcs-api` will be documented in this file.

## Release v1.02 - 2024-10-22

Description of what’s new in v1.02

## 22 Oct 2024

- Added AcceptOrder and tagged a new version 1.02

## 19 Oct 2024

- Moved the repo from `fintech-systems/whmcs-php-api` to `eugenefvdm/whmcs-api`
- Updated the README to specify that URLs should not have a trailing slash
- Upgraded Pest so that we can use `->todo()`
- Changed the WHMCS test installation test from `->skip()` to `->todo()`
- Took out the section `Why` in the readme
- Upgraded these composer libraries: `pest`, `orchestra test bench`, `dotenv`, `spatie-ray`
- Bumped the PHP version to `8.2`
- Followed various PhpStorm IDE suggestions to improve the code
- Added a new environment setting `WHMCS_DEBUG`
- Created an automated test for adding new orders

## Unreleased

- Fix typo in readme when hooking up for local development
- Added back some ray calls in API and remove an old comment

## v0.1.2 - 2022-10-26

- Also ask for setting when retrieving registrar values

## v0.1.1 - 2022-10-26

- Added a custom action setregistrarvalue
- Added a helper to add custom action to the WHMCS API, for now overwriting UpdateClientAddon
- Added updateClientDomain command in main API
- Added direct links to WHMCS manual for all commands
- Added exception handling in constructor if the environment is not set
- Moved custom API command to /includes/api to mirror WHMCS setup
- The custom API action now have rudimentary logging
- The README has been updated to reflect how to test when doing custom actions
- GetClientByPhoneNumber doesn't return both result "success" and message "ok". Only result.
- Simplified all commands to be less lines long by removing redundant action variable
- Most commands and now alphabetical with private methods at the bottom
- API limits are now a global variable fixed to 10000

## v0.1.0 - 2022-10-23

- Added a test for getDomains
- Added GitHub Action workflow for testing. It took hours because of .env issues and the unknown parameters to use. Copied from PayFast and eventually got it working.
- Updated all tests to use mocks
- Updated composer
- Fixed README to specify Pest is actually the test suite
- Fixed the current test suite to a test and a live system. It turns out WHMCS has an inconsistent way of saving telephone numbers, perhaps long ago no spaces but no it auto injects spaces for some countries. USA numbers also seems to selectively have dashes and sometimes not. This means some of the tests could be inconsistent in real-life and on other systems. Got 11 tests working but used data injected from the tests.

## v0.0.5 - 2021-10-27

- refactor to remove return statements in getclientbyphonenumber as return statement might lead to abnormal program termination via the include system

## v0.0.4 - 2021-10-27

- handling of space in telephone numbers

## v0.0.3 - 2021-10-24

- added ability to switch to another server (setServer(url, api identifier, api secret)
- added tests to test setServer ability
- updated .env.example with hypothetical server credentials environment variables

## v0.0.1 - 2021-10-23

- initial release
