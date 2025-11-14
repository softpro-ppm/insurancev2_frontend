# 🔄 Sync Production Policies to Local - Complete Guide

This guide will help you export all 548 policies from your Hostinger production server and import them into your local development database.

## 📋 Overview

- ✅ Export production policies (data only, no files)
- ✅ Export production renewals
- ✅ Clear local database
- ✅ Import all production data locally

---

## 🚀 Step 1: Deploy to Production

### Push to GitHub:
```bash
cd /Users/rajesh/Documents/GitHub/insurancev2_frontend
git push origin main
```

### Deploy to Hostinger:
Go to your Hostinger Git deployment or manually upload these files:
- `app/Console/Commands/ExportPolicies.php` (new file)

---

## 📤 Step 2: Export from Production

### Option A: Via SSH (Recommended)
```bash
# SSH into your Hostinger server
ssh your-username@your-server

# Navigate to your project
cd /domains/v2insurance.softpromis.com/public_html

# Run the export command
php artisan policies:export

# Download the file (use SFTP/FileZilla or copy content)
cat policies_export.json
```

### Option B: Via Hostinger Terminal
1. Go to Hostinger Control Panel
2. Open **Terminal** or **SSH Access**
3. Navigate to project: `cd public_html`
4. Run: `php artisan policies:export`
5. Download `policies_export.json` via File Manager

### Option C: Create a Web Script (if SSH not available)
Upload this file to your Hostinger:

**File: `run_export.php`** (in public_html root)
```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

exec('cd ' . base_path() . ' && php artisan policies:export 2>&1', $output, $return);

header('Content-Type: text/plain');
echo "Export Command Output:\n";
echo "======================\n";
echo implode("\n", $output) . "\n";
echo "======================\n";
echo "Return Code: {$return}\n\n";

if ($return === 0) {
    $exportFile = base_path('policies_export.json');
    if (file_exists($exportFile)) {
        echo "✅ Export successful!\n";
        echo "File size: " . round(filesize($exportFile) / 1024 / 1024, 2) . " MB\n\n";
        echo "Download link:\n";
        echo "https://v2insurance.softpromis.com/policies_export.json\n\n";
        echo "⚠️ IMPORTANT: Delete run_export.php and policies_export.json after downloading!\n";
    }
}
?>
```

Then visit: `https://v2insurance.softpromis.com/run_export.php`

---

## 📥 Step 3: Download the Export File

Download `policies_export.json` from production and save it to your local project root:
```
/Users/rajesh/Documents/GitHub/insurancev2_frontend/policies_export.json
```

---

## 💻 Step 4: Import to Local Database

Run the import script:
```bash
cd /Users/rajesh/Documents/GitHub/insurancev2_frontend
php import_policies_local.php
```

You'll see:
```
====================================
Policy Import Tool - Production to Local
====================================

📂 Reading export file...
✅ Found 548 policies to import
✅ Found XX renewals to import
📅 Exported at: 2025-10-27 XX:XX:XX

⚠️  WARNING: This will:
   1. DELETE all existing local policies
   2. DELETE all existing local renewals
   3. IMPORT 548 policies from production
   4. IMPORT XX renewals from production

Do you want to continue? (yes/no): 
```

Type `yes` and press Enter.

---

## ✅ Step 5: Verify Import

Check your local database:
```bash
php artisan tinker --execute="
echo 'Local Policies: ' . \App\Models\Policy::count() . PHP_EOL;
echo 'Local Renewals: ' . \App\Models\Renewal::count() . PHP_EOL;
"
```

Expected output:
```
Local Policies: 548
Local Renewals: XX
```

---

## 🧹 Step 6: Cleanup

### On Production (Hostinger):
Delete these files for security:
- `policies_export.json`
- `run_export.php` (if you created it)
- `export_policies_production.php` (if you uploaded it)
- `check_production_renewals.php` (if you uploaded it)

### On Local:
```bash
# Delete the export file (it's already in .gitignore)
rm /Users/rajesh/Documents/GitHub/insurancev2_frontend/policies_export.json
```

---

## 📊 What Gets Synced

### ✅ Included:
- All policy data (548 policies)
- Customer information
- Vehicle/Health details
- Policy dates, premiums, commissions
- Renewal records
- Notes and status

### ❌ NOT Included:
- Document files (PDFs, images)
- File paths (will be empty in local)

---

## 🔧 Troubleshooting

### Error: "Command not found: php artisan policies:export"
**Solution:** Make sure you've deployed the new `ExportPolicies.php` command to production.

### Error: "policies_export.json not found"
**Solution:** Make sure the file is downloaded and placed in the project root, renamed to exactly `policies_export.json`.

### Error: "Mass assignment exception"
**Solution:** Check that your Policy and Renewal models have the correct `$fillable` fields.

### Import shows errors
**Solution:** Check the specific error messages. Common issues:
- Missing required fields
- Invalid date formats
- Database constraint violations

---

## 🎯 Quick Reference

| Step | Command | Location |
|------|---------|----------|
| 1. Export | `php artisan policies:export` | Production (Hostinger) |
| 2. Download | Via SFTP/File Manager | Production → Local |
| 3. Import | `php import_policies_local.php` | Local |
| 4. Verify | `php artisan tinker --execute="..."` | Local |

---

## 🔐 Security Notes

- ⚠️ The export file contains sensitive customer data
- ⚠️ Delete export files immediately after use
- ⚠️ Never commit `policies_export.json` to Git (already in .gitignore)
- ⚠️ Delete any temporary web scripts from production

---

## ✨ After Import

Your local database will now have all 548 production policies! You can:
- Test the Follow Ups page locally with real data
- Develop new features with production-like data
- Test reports and analytics

---

**Need Help?**
If you encounter any issues, check the error messages carefully and refer to the troubleshooting section above.

