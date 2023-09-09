# Realtime Randomization

This module allows you to randomize a record using REDCap's normal allocation-based methods automatically when a form is saved.

## Controlling when Randomization Occurs
You can control when the module will try to randomize by specifying:
* an optional specific form
  - only when this form is saved will the module run
* a specific event
  - only when saved on this event will it run
* and specific logic, such as `[consented] = '1'`

[!NOTE]
All specified filters must be true in order for the Realtime Randomization to take place.  Leave a filter blank to ignore it.

## Configuration Errors
Common configuration issues or error messages are logged to the project logs.  Additional debug logging is available with the use of the emLogger module.

## User Beware
This module uses some core REDCap methods not documented for EM usage.  As a result, there is a higher chance future updates to REDCap could affect this project.  Use of this module should be tested before/after each upgrade for critical projects.

### Changes / Fixes
| Date       | Comment                                                                                           |
|------------|---------------------------------------------------------------------------------------------------|
| 2021-12-13 | Fix to empty value checking so '0' (e.g. as stratum or allocation value) is treated as not empty. |
| 2021-04-26 | Fixed a bug where smart-variables using the instrument name were failing                          |
| 2023-02-21 | Updating framework to version 9 and releasing version 1.0.2                                       |
| 2023-09-10 | Updated framework to 13 and README                                                                |
