# 🚀 QUICK SYNC GUIDE - Step by Step

## ✅ STEP 1: TEST EXPORT LOCALLY (2 minutes)

### 1.1: Make sure your local server is running
```bash
# In terminal, you should see:
# INFO  Server running on [http://127.0.0.1:8003]
```

### 1.2: Open browser and visit:
```
http://127.0.0.1:8003/SYNC_EXPORT_LOCAL.php
```

### 1.3: You should see:
- ✅ "🔄 Local Database Export"
- ✅ "Found 559 policies"
- ✅ "Found 0 policy versions" (or some number)
- ✅ "Found 2 agents"
- ✅ "✅ EXPORT COMPLETED SUCCESSFULLY!"
- ✅ File size: ~500KB - 2MB

### 1.4: Check your project folder
- Open Finder → `insurancev2_frontend` folder
- Look for: `local_to_production.sql`
- ✅ File exists = Export worked!

---

## 📤 STEP 2: DEPLOY TO PRODUCTION (5 minutes)

### 2.1: Open GitHub Desktop

### 2.2: You should see these files:
- ✅ `public/SYNC_EXPORT_LOCAL.php` (modified)
- ✅ `public/SYNC_IMPORT_PRODUCTION.php` (modified)
- ✅ `local_to_production.sql` (NEW - this is your database!)

### 2.3: Commit and Push:
1. **Write commit message:** "Add sync scripts and exported database"
2. Click **"Commit to main"**
3. Click **"Push origin"**

### 2.4: Deploy on Hostinger:
1. Go to **Hostinger Panel** → **GIT** section
2. Find `insurancev2_frontend` repository
3. Click **"Deploy"** button
4. Wait for: **"Deployment end"** ✅

---

## 🔄 STEP 3: IMPORT ON PRODUCTION (3 minutes)

### 3.1: Open browser and visit:
```
https://v2insurance.softpromis.com/SYNC_IMPORT_PRODUCTION.php
```

### 3.2: You should see progress:
- 🗑️ "Cleared table: renewals"
- 🗑️ "Cleared table: policies"
- 🗑️ "Cleared table: policy_versions"
- 🗑️ "Cleared table: agents"
- ✅ "Executed XXX SQL statements"
- ✅ "✅ IMPORT COMPLETED SUCCESSFULLY!"

### 3.3: Verify counts shown:
- ✅ Policies: **559**
- ✅ Policy Versions: **0** (or whatever local had)
- ✅ Agents: **2**
- ✅ Renewals: **0** (cleared!)

---

## ✅ STEP 4: VERIFY IN PRODUCTION (5 minutes)

### 4.1: Clear Production Cache
Visit:
```
https://v2insurance.softpromis.com/clear-all-cache-now
```
You should see: **"✅ All caches cleared successfully!"**

### 4.2: Quit and Restart Browser
- **Quit browser completely:** Cmd+Q (Mac) or Alt+F4 (Windows)
- **Restart browser**
- **Login to:** `https://v2insurance.softpromis.com`

### 4.3: Test Renewals Page ✅
1. Click **"Renewals"** in sidebar
2. **Check what you see:**
   - ✅ Should show: **"PENDING"** or **"IN PROGRESS"** status
   - ❌ Should NOT show: **"RENEWED"** status
   - ✅ Completed Renewals: **0**
   - ✅ Pending Renewals: **~54** (policies expiring in 30 days)

### 4.4: Test Policy Type Filter ✅
1. Click **"Policies"** in sidebar
2. **Select "Health"** from Policy Type dropdown
3. **Counts should update:**
   - Active Policies: ~14
   - Total Policies: ~14
4. **Select "Motor"** → Counts update to ~545
5. **Select "All"** → Shows all 559

### 4.5: Test Dashboard ✅
1. Click **"Dashboard"** in sidebar
2. **Check stats:**
   - Total Policies: **559**
   - Active Policies: **~500**
   - Expired Policies: **~50**
   - Pending Renewals: **~54**

---

## 🧹 STEP 5: CLEANUP (SECURITY - IMPORTANT!)

### 5.1: Delete Sync Files from Production

**Option A: Via Hostinger File Manager (Easiest)**
1. Go to **Hostinger Panel** → **File Manager**
2. Navigate to: `/public_html/v2insurance/public/`
3. **Delete these files:**
   - `SYNC_EXPORT_LOCAL.php`
   - `SYNC_IMPORT_PRODUCTION.php`
4. Navigate to: `/public_html/v2insurance/`
5. **Delete:**
   - `local_to_production.sql`

**Option B: Via GitHub (Recommended)**
1. In your local project folder, delete:
   - `public/SYNC_EXPORT_LOCAL.php`
   - `public/SYNC_IMPORT_PRODUCTION.php`
   - `local_to_production.sql`
2. In **GitHub Desktop:**
   - Commit message: "Remove sync scripts after successful sync"
   - Click **"Commit to main"**
   - Click **"Push origin"**
3. **Deploy on Hostinger** again

---

## ✅ SUCCESS CHECKLIST

After completing all steps, verify:

- [ ] Local export worked (saw 559 policies)
- [ ] SQL file created in project folder
- [ ] Committed and pushed to GitHub
- [ ] Deployed to Hostinger
- [ ] Production import completed (saw success message)
- [ ] Renewals page shows "PENDING" (NOT "RENEWED")
- [ ] Policy Type filter works (Health/Motor/All)
- [ ] Dashboard counts match local
- [ ] Sync files deleted from production

---

## 🆘 TROUBLESHOOTING

### ❌ "404 NOT FOUND" on export
**Solution:** Make sure server is running on port 8003:
```bash
php artisan serve
```

### ❌ "SQL file not found" on import
**Solution:** Make sure you deployed `local_to_production.sql` to production via GitHub Desktop

### ❌ Still seeing "RENEWED" status
**Solution:**
1. Clear cache: `/clear-all-cache-now`
2. Quit browser completely (Cmd+Q)
3. Restart browser
4. Hard refresh: Cmd+Shift+R

### ❌ Import takes too long
**Solution:** Normal for 559 policies. Wait up to 2-3 minutes. Don't close browser!

---

**Ready to start? Begin with STEP 1!** 🚀

