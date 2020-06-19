# Realtime Randomization

This module allows you to randomize a record using REDCap's normal allocation-based methods automatically when a form is saved.

You can control when the module will try to randomize by specifying:
* a specific form
* a specific event
* or specific logic, such as `[consented] = '1'`

Common configuration issues or error messages are logged to the project logs.  Additional debug logging is available with the use of the emLogger module.
