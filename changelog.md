# Fields
- Added new field 'automation_list_name' on automations
    ->migrate field up

->Subscribers
    ->add notes to PyroAdmin UI


1. Sync should  check local users first against remote
2. Sync should then do a pull to check remote against local

Test by removing push to remote


# Remote Checks
### Check 1
- Local = Remote: OK Goto Local Checks


### Check 2
Local < Remote: OK Goto Local Checks


### Check 3
- Local > Remote: `ERROR`
- Action: `None`
- Status: `RESOLVE` (User to Resolve)
- Description: Record out of Sync, By setting this flag, the user can now resolve the indivudal user by deciding to pull or push data to resolve sync.


# Local Checks