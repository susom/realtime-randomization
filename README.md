# Realtime Randomization

This module allows you to randomize a record using REDCap's normal allocation-based methods automatically when a form is saved.

You can control when the module will try to randomize by specifying:
* a specific form
* a specific event
* or specific logic, such as `[consented] = '1'`

Common configuration issues or error messages are logged to the project logs.  Additional debug logging is available with the use of the emLogger module.

### Notes
This module uses some core REDCap methods not documented for EM usage.  As a result, there is a higher chance future updates to REDCap could affect this project.  Use of this module should be tested before/after each upgrade.

### Fixes
2021-12-13 Fix to empty value checking so '0' (e.g. as stratum or allocation value) is treated as not empty.
2021-04-26 Fixed a bug where smart-variables using the instrument name were failing
2023-02-21 Updating framework to version 9 and releasing version 1.0.2
