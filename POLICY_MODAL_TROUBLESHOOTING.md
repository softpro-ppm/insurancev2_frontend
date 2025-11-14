# Policy Modal Troubleshooting Guide

## Issue
The add motor/health/life policy modals are not working - showing "Failed to submit policy. Please try again." error.

## Changes Made

### 1. Enhanced Error Logging in `public/js/app.js`
- Added detailed console logging to track form submission
- Added CSRF token validation before submission
- Improved error messages to show specific server errors
- Added FormData contents logging

### 2. Diagnostic Script Created
File: `public/js/policy-modal-fix.js`
- Can be run in browser console to diagnose issues

## How to Diagnose the Issue

### Step 1: Clear Browser Cache
1. Open the application in your browser
2. Press `Ctrl+Shift+R` (Windows) or `Cmd+Shift+R` (Mac) to hard refresh
3. This ensures the updated JavaScript files are loaded

### Step 2: Open Browser Console
1. Press `F12` or right-click > "Inspect"
2. Go to the "Console" tab
3. Keep it open while testing

### Step 3: Test Policy Creation
1. Click "Add New Policy" button
2. Select a policy type (Motor/Health/Life)
3. Click "Next"
4. Select business type (Self/Agent)
5. Click "Next"
6. Fill in all required fields:
   - Customer Name
   - Phone Number (must be exactly 10 digits)
   - Company Name
   - Insurance Type
   - Start Date
   - End Date
   - Premium (must be > 0)
   - Customer Paid Amount (must be > 0)
   - For Motor: Vehicle Number and Vehicle Type
7. Click "Add Policy"

### Step 4: Check Console Output
Look for these log messages:
```
===============================================
HandlePolicySubmit: Form submission started
HandlePolicySubmit: Active policy type: Motor/Health/Life
HandlePolicySubmit: CSRF token found: ...
Creating policy with files - FormData contents:
```

If you see errors, look for:
- **CSRF token missing**: Refresh the page
- **Validation errors**: Check which fields are failing
- **Network errors**: Check your internet connection
- **Server errors**: Check Laravel logs

### Step 5: Check Network Tab
1. In Developer Tools, go to "Network" tab
2. Try submitting the policy again
3. Look for a request to `/policies`
4. Click on it to see:
   - Status code (should be 200 or 201 for success)
   - Response body (will show error details if failed)

## Common Issues and Solutions

### Issue: "CSRF token missing"
**Solution**: 
```bash
# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Issue: "Phone number must be exactly 10 digits"
**Solution**: Ensure phone number has exactly 10 digits, no spaces or special characters

### Issue: "The vehicle number field is required"
**Solution**: For Motor policies, vehicle number and type are mandatory

### Issue: Server returns 422 (Validation Error)
**Solution**: Check the response body in Network tab to see which fields failed validation

### Issue: Server returns 419 (CSRF Token Mismatch)
**Solution**: 
1. Check if session is working
2. Clear browser cookies
3. Restart Laravel server

### Issue: Server returns 500 (Internal Server Error)
**Solution**: Check Laravel logs
```bash
tail -f storage/logs/laravel.log
```

## Manual Test in Browser Console

Run this script in the browser console to diagnose:

```javascript
// Load the diagnostic script
const script = document.createElement('script');
script.src = '/js/policy-modal-fix.js';
document.head.appendChild(script);
```

## Files Modified
1. `public/js/app.js` - Added better error handling and logging
2. `public/js/policy-modal-fix.js` - New diagnostic script

## Next Steps if Issue Persists

1. **Share Console Logs**: Copy all console messages and share them
2. **Share Network Tab**: Take a screenshot of the failed request in Network tab
3. **Check Laravel Logs**: Run `tail -50 storage/logs/laravel.log` and share the output
4. **Test with Minimal Data**: Try submitting with only required fields filled

## Verification After Fix

To verify the issue is resolved:
1. Hard refresh the page (`Ctrl+Shift+R`)
2. Open console
3. Try creating a Motor policy with minimal required fields
4. Try creating a Health policy
5. Try creating a Life policy
6. All three should work without errors

## Contact
If the issue persists after following these steps, please provide:
- Browser console logs (full output)
- Network tab screenshot showing the failed request
- Laravel log entries (last 50 lines)

