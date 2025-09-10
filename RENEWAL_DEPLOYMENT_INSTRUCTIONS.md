# 🚀 DEPLOYMENT INSTRUCTIONS - RENEWAL FUNCTIONALITY UPDATE

## ✅ READY TO DEPLOY FILES:

1. **`production_deployment_with_renewals.zip`** - Complete Laravel application with renewal functionality
2. **`production.env`** - Production environment file with correct APP_KEY
3. **`run_renewal_seeder.php`** - Script to populate renewal data

## 📋 EXACT STEPS TO FIX YOUR WEBSITE:

### Step 1: Download Files from Your Computer
1. Go to your project folder: `/Users/rajesh/Documents/GitHub/insurancev2_frontend/`
2. You'll find these files:
   - `production_deployment_with_renewals.zip` (your complete website with renewal functionality)
   - `production.env` (environment configuration)
   - `run_renewal_seeder.php` (script to add renewal data)

### Step 2: Upload to Hostinger
1. **Login to Hostinger Control Panel**
   - Go to: https://hpanel.hostinger.com/
   - Login with your credentials

2. **Open File Manager**
   - Click on "File Manager"
   - Navigate to: `/domains/v2.insurance.softpromis.com/public_html/`

3. **Clear Existing Files (IMPORTANT)**
   - Select all files in `public_html` folder
   - Delete them (backup if needed)

4. **Upload New Files**
   - Upload `production_deployment_with_renewals.zip`
   - Extract the zip file in `public_html`
   - Upload `production.env` file
   - Upload `run_renewal_seeder.php` file
   - Rename `production.env` to `.env` (with the dot at the beginning)

### Step 3: Set File Permissions
1. Right-click on `.env` file → Set permissions to `644` or `600`
2. Right-click on `run_renewal_seeder.php` → Set permissions to `644`

### Step 4: Run the Renewal Seeder (IMPORTANT!)
1. Visit: https://v2.insurance.softpromis.com/run_renewal_seeder.php
2. You should see: "✅ RenewalSeeder completed successfully!"
3. **Delete the `run_renewal_seeder.php` file after running it** (for security)

### Step 5: Test Your Website
1. Visit: https://v2.insurance.softpromis.com/dashboard
2. The "Recent Renewals" table should now show data ✅
3. You should see renewal records with customer names, premiums, due dates, etc.

---

## 🔧 WHAT'S NEW IN THIS UPDATE:

### ✅ **Dashboard Changes:**
- **"Recent Policies"** table → **"Recent Renewals"** table
- **"Premium vs Revenue vs Policy"** → **"Premium vs Revenue vs Policies"** (fixed plural)

### ✅ **New Renewal Data:**
- 50+ sample renewal records
- Customer names, phone numbers, policy types
- Current premiums, renewal premiums, due dates
- Status badges (Pending, Completed, Overdue, Scheduled)
- Action buttons (Edit, Delete, View)

### ✅ **Enhanced Functionality:**
- Search renewals by any field
- Sort by any column
- Pagination controls
- Responsive design

---

## 🆘 TROUBLESHOOTING

**If the renewal table is still empty after deployment:**

1. **Check if seeder ran successfully:**
   - Visit: https://v2.insurance.softpromis.com/run_renewal_seeder.php
   - Should show: "📈 Total renewals in database: 50"

2. **Check browser console:**
   - Press F12 → Console tab
   - Look for: "✅ Renewals data received: {renewals: Array(50)}"
   - If you see "Array(0)", the seeder didn't run properly

3. **Manual database check:**
   - Create file: `check_renewals.php`
   - Content:
   ```php
   <?php
   require_once 'bootstrap/app.php';
   $count = \App\Models\Renewal::count();
   echo "Renewals in database: " . $count;
   ?>
   ```
   - Visit: https://v2.insurance.softpromis.com/check_renewals.php
   - Delete the file after checking

**If website shows errors:**
- Check `.env` file exists and has correct APP_KEY
- Verify file permissions are set correctly
- Clear browser cache and refresh

---

## ✅ VERIFICATION CHECKLIST

After deployment, verify these work:
- [ ] Website loads without errors
- [ ] Dashboard shows "Recent Renewals" table (not "Recent Policies")
- [ ] Renewal table displays data (not empty)
- [ ] Chart title shows "Premium vs Revenue vs Policies" (with 's')
- [ ] Search functionality works in renewal table
- [ ] Sort functionality works in renewal table
- [ ] Action buttons work (Edit, Delete, View)

---

## 📞 SUPPORT

If you need help:
1. Send me screenshot of any errors
2. Tell me which step you're stuck on
3. I'll help you resolve it immediately

**Your renewal functionality will be working once you complete these steps!** 🎉
