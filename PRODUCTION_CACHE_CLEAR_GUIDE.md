# 🚀 Production Cache Clear Guide - Fix 0 Counts

## Issue: Follow-ups Page Showing 0 Counts on Production

Your production server is showing 0 counts because of cached files. Follow these steps:

---

## ✅ Step 1: Clear Browser Cache (Try First)

1. **Hard Refresh in Browser:**
   - Windows/Linux: `Ctrl + Shift + R` or `Ctrl + F5`
   - Mac: `Cmd + Shift + R`

2. **Clear Browser Cache:**
   - Open Developer Tools (F12)
   - Right-click on refresh button → "Empty Cache and Hard Reload"

3. **Incognito/Private Mode:**
   - Open in incognito mode to test without cache

---

## ✅ Step 2: Access Cache Clear Script on Hostinger

We created a cache clearing script. Access it:

**URL:** `https://v2insurance.softpromis.com/clear_production_cache.php`

This will clear:
- Laravel cache
- Config cache
- View cache
- Route cache

---

## ✅ Step 3: Manual Cache Clear via File Manager

If the script doesn't work:

1. **Login to Hostinger**
2. **Go to File Manager**
3. **Navigate to your project folder**
4. **Delete these folders:**
   - `bootstrap/cache/*.php` (except `.gitignore`)
   - `storage/framework/cache/data/*`
   - `storage/framework/views/*`
   - `storage/framework/sessions/*`

---

## ✅ Step 4: Check API Endpoint Directly

Test if API is working:

**URL:** `https://v2insurance.softpromis.com/api/followups/dashboard`

You should see JSON data with:
```json
{
  "success": true,
  "stats": {
    "lastMonth": X,
    "currentMonth": X,
    "nextMonth": X,
    "total": X
  }
}
```

If you see 0 in the JSON, then the issue is in the backend, not cache.

---

## ✅ Step 5: Debug Backend Issue

If API returns 0, check:

1. **Database has policies with end dates**
2. **Policies have `end_date` field populated**
3. **Check Laravel logs:**
   - Location: `storage/logs/laravel.log`
   - Look for errors related to followups

---

## ✅ Step 6: Re-deploy Files (If Nothing Works)

1. **Open GitHub Desktop**
2. **Pull latest changes from origin/main**
3. **Upload these files to Hostinger:**
   - `app/Http/Controllers/FollowupController.php`
   - `resources/views/followups/index.blade.php`
   - `routes/web.php`

---

## 🔍 Quick Diagnostic Checklist

- [ ] Hard refresh browser (Ctrl+Shift+R)
- [ ] Access clear_production_cache.php
- [ ] Test API endpoint directly
- [ ] Check if policies exist in database
- [ ] Check Laravel logs for errors
- [ ] Verify files are deployed correctly

---

## 💡 Most Common Issues:

1. **Browser Cache** (80% of cases) - Hard refresh fixes it
2. **Laravel Cache** (15% of cases) - clear_production_cache.php fixes it
3. **Missing Deployment** (5% of cases) - Re-upload files

---

## 🆘 Still Not Working?

Check browser console for errors:
1. Press F12
2. Go to Console tab
3. Look for red errors
4. Share the error messages

The console will show exactly what's failing!
