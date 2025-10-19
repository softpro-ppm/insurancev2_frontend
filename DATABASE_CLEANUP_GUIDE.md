# Database Cleanup Guide - Fix Policy Version History

## The Problem
You're seeing multiple versions (2, 3, 4) because the **database still contains old policy versions**. Our code changes only affect how versions are displayed, but the actual version records are still in the database.

## The Solution
We need to **delete the old policy versions from the database** so that only the current policy data remains.

## Two Ways to Clean Up:

### Option 1: Web-Based Cleanup (Easiest)
1. **Upload** `cleanup_versions_web.php` to your live server root directory
2. **Access** via browser: `https://v2insurance.softpromis.com/cleanup_versions_web.php`
3. **Click** "Delete All Policy Versions" button
4. **Confirm** the action
5. **Refresh** your browser and test policy history

### Option 2: Command Line Cleanup
1. **Upload** `cleanup_policy_versions_live.php` to your live server
2. **Run** via SSH: `php cleanup_policy_versions_live.php`
3. **Refresh** your browser and test policy history

## What This Will Do:
- ✅ **Delete ALL old policy versions** from the database
- ✅ **Keep all current policy data** intact
- ✅ **Policy history will show only Version 1** for each policy
- ✅ **No more multiple versions** from bulk uploads

## After Cleanup:
1. **Refresh your browser** (Ctrl+F5 or Cmd+Shift+R)
2. **Go to Policies page**
3. **Click "View History"** on any policy
4. **Should show only Version 1** instead of multiple versions

## Files to Upload:
- `cleanup_versions_web.php` - Web-based cleanup (recommended)
- `cleanup_policy_versions_live.php` - Command line cleanup

## Expected Result:
After cleanup, your policy history will show only **1 version** instead of 2, 3, or 4 versions!

The issue is that we've been deploying code changes but not cleaning up the actual database records. This cleanup will fix that! 🚀
