# Production Cache Clear Instructions

## ✅ **Fixes Applied:**

### 1. **Time Display Fix**
- Changed time format from full date/time to only "HH:MM am/pm"
- Updated cache version from `v=2.10` to `v=2.11` in `insurance.blade.php`
- Fixed time update interval to run every minute instead of every second

### 2. **Date Filtering Fix**
- Fixed date range calculation for different periods
- Improved backend query logic for date filtering
- Added better logging for debugging

---

## 🚀 **Steps to Clear Cache on Production:**

### **Step 1: Clear Browser Cache (User Side)**
1. Open browser Developer Tools (F12)
2. Right-click on the refresh button
3. Select "Empty Cache and Hard Reload" or "Clear Cache and Hard Reload"
4. Or use Ctrl+Shift+R (Windows/Linux) or Cmd+Shift+R (Mac)

### **Step 2: Clear Laravel Cache (Server Side)**

**Via SSH:**
```bash
ssh -p 65002 u820431346@145.14.146.15
cd ~/public_html/v2insurance

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Step 3: Clear Browser Cache via Browser Settings**

**Chrome/Edge:**
1. Press `Ctrl+Shift+Delete` (Windows) or `Cmd+Shift+Delete` (Mac)
2. Select "Cached images and files"
3. Time range: "All time"
4. Click "Clear data"

**Firefox:**
1. Press `Ctrl+Shift+Delete` (Windows) or `Cmd+Shift+Delete` (Mac)
2. Select "Cache"
3. Time range: "Everything"
4. Click "Clear Now"

**Safari:**
1. Safari → Preferences → Advanced
2. Check "Show Develop menu"
3. Develop → Empty Caches

---

## 🔍 **Verify Fixes:**

### **Time Display:**
- Should show only: "08:43 am" (not full date/time)
- Updates every minute

### **Date Filtering:**
1. Open browser console (F12)
2. Go to "My Business Analytics" page
3. Change dropdown from "This Month" to "This Year"
4. Check console logs - should show different date ranges:
   - "This Month": Start date = 1st of current month
   - "This Year": Start date = April 1 (financial year)
5. Data should be different for each period

---

## 📝 **Files Changed:**

1. `public/js/app.js` - Time display function and date filtering logic
2. `resources/views/layouts/insurance.blade.php` - Cache version updated to v2.11
3. `app/Http/Controllers/BusinessAnalyticsController.php` - Improved date filtering query

---

## ⚠️ **If Still Not Working:**

1. **Check browser console** for JavaScript errors
2. **Check Laravel logs** (`storage/logs/laravel.log`) for backend errors
3. **Verify file versions** - Make sure `app.js?v=2.11` is loading
4. **Try incognito/private mode** to bypass browser cache completely
5. **Check server logs** for any PHP errors

---

## 🎯 **Quick Test:**

After clearing cache:
1. Refresh page with Ctrl+Shift+R
2. Check time display - should be "HH:MM am/pm" format
3. Change dropdown filter - should see different data
4. Check browser console for date range logs

