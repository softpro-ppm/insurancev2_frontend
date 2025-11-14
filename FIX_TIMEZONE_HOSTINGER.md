# Fix Timezone to IST (Asia/Kolkata) on Hostinger

## ✅ **What Was Fixed:**

Updated `config/app.php` to use `'timezone' => 'Asia/Kolkata'` (IST, UTC+5:30)

---

## 🚀 **Steps to Deploy to Hostinger:**

### **Step 1: Commit & Push (GitHub Desktop)**

1. Open **GitHub Desktop**
2. You'll see `config/app.php` has changes
3. **Commit message:** `Fix timezone to IST (Asia/Kolkata)`
4. Click **"Commit to main"**
5. Click **"Push origin"** button
6. Wait 1-2 minutes for Hostinger to auto-deploy

---

### **Step 2: Clear Cache on Server (SSH)**

After deployment, connect via SSH and run:

```bash
ssh -p 65002 u820431346@145.14.146.15
cd ~/public_html/v2insurance
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

This ensures Laravel picks up the new timezone setting.

---

### **Step 3: Verify Timezone**

You can verify the timezone is set correctly by:

**Option A: Via SSH**
```bash
cd ~/public_html/v2insurance
php artisan tinker
echo config('app.timezone');
exit
```

Should output: `Asia/Kolkata`

**Option B: Via Website**
- Check dates/times in your application
- They should now show in IST (UTC+5:30)

---

## 📋 **What Changed:**

### **Before:**
```php
'timezone' => 'UTC',
```

### **After:**
```php
'timezone' => 'Asia/Kolkata',
```

---

## ✅ **Expected Results:**

After this fix:
- ✅ All dates will show in IST (Indian Standard Time)
- ✅ Database timestamps will use IST
- ✅ Policy creation dates will be in IST
- ✅ All time displays will be UTC+5:30

---

## 🔍 **If Timezone Still Shows Wrong:**

### **Check PHP Timezone (via SSH):**

```bash
cd ~/public_html/v2insurance
php -r "echo date_default_timezone_get();"
```

Should output: `Asia/Kolkata` or `UTC`

If it shows `UTC`, you may need to set it at PHP level:

**Edit `.htaccess` in public folder:**
```apache
<IfModule mod_php7.c>
    php_value date.timezone "Asia/Kolkata"
</IfModule>
```

Or contact Hostinger support to set PHP timezone to `Asia/Kolkata`.

---

## 📝 **Summary:**

1. ✅ **Code updated:** `config/app.php` → `Asia/Kolkata`
2. ✅ **Deploy:** Commit & push via GitHub Desktop
3. ✅ **Clear cache:** Run cache clear commands via SSH
4. ✅ **Verify:** Check dates in your application

**Total time:** 5 minutes

---

## 🆘 **Troubleshooting:**

### **Problem:** Timezone still showing wrong
**Solution:** Clear all Laravel caches via SSH:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
```

### **Problem:** Can't connect via SSH
**Solution:** Use phpMyAdmin or Hostinger file manager to check/cache config

### **Problem:** PHP timezone different from Laravel
**Solution:** Contact Hostinger support to set PHP timezone to `Asia/Kolkata`

---

**Last Updated:** October 31, 2025

