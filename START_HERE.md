# 🚀 START HERE - Quick Guide to Sync Production Data

I've created everything you need! Follow these 3 simple steps:

---

## 📦 **STEP 1: Upload to Hostinger**

I've created: **`production_sync_package.zip`** (5 KB)

### Upload these 2 files to your Hostinger:

1. **`run_production_export.php`** → Upload to:
   ```
   /domains/v2insurance.softpromis.com/public_html/run_production_export.php
   ```

2. **`app/Console/Commands/ExportPolicies.php`** → Upload to:
   ```
   /domains/v2insurance.softpromis.com/public_html/app/Console/Commands/ExportPolicies.php
   ```

**How to upload:**
- Go to Hostinger Control Panel
- Click "File Manager"
- Navigate to `public_html`
- Click "Upload" and select the files
- Or extract `production_sync_package.zip` and drag files

---

## 🌐 **STEP 2: Run the Export**

**Visit this URL in your browser:**
```
https://v2insurance.softpromis.com/run_production_export.php?password=export2025
```

You'll see:
- ✅ Found 548 policies...
- ✅ Export completed!
- 📥 Download button

**Click the download button** and save as:
```
/Users/rajesh/Documents/GitHub/insurancev2_frontend/policies_export.json
```

---

## 💬 **STEP 3: Tell Me When Ready**

Once you've downloaded `policies_export.json`, just say:
- **"downloaded"** or
- **"ready to import"** or
- **"got the file"**

And I'll automatically run the import to your local database!

---

## ✅ What Will Happen

After import, your local database will have:
- **548 policies** (all production data)
- **All renewals** 
- **All customer information**
- **Vehicle/Health details**

---

## 🔐 Security Reminder

After downloading, **DELETE from Hostinger:**
- `run_production_export.php`
- `policies_export.json`

(I'll remind you again after the import!)

---

## 📍 Files Created

✅ **production_sync_package.zip** - Ready to upload
✅ **import_policies_local.php** - Import script (already on local)
✅ **SYNC_PRODUCTION_TO_LOCAL_GUIDE.md** - Detailed guide

---

**Ready? Upload the files and visit the URL! 🚀**

