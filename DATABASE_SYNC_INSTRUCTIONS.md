# DATABASE SYNC INSTRUCTIONS
## Local → Production Complete Sync

**Purpose:** Make production database identical to local database

**What Gets Synced:**
- ✅ Policies table (559 records)
- ✅ Policy Versions table (0 records - fresh start)
- ✅ Agents table (2 agents)
- ✅ Renewals table (CLEARED - no "RENEWED" status)

**What Stays Unchanged:**
- ✅ Users table (admin account preserved)
- ✅ Sessions (login stays active)
- ✅ Migrations (already synchronized)

---

## STEP-BY-STEP PROCESS

### PHASE 1: EXPORT FROM LOCAL (5 minutes)

#### Step 1.1: Commit Sync Scripts to GitHub

1. Open **GitHub Desktop**
2. You should see 2 new files:
   - `SYNC_EXPORT_LOCAL.php`
   - `SYNC_IMPORT_PRODUCTION.php`
   - `DATABASE_SYNC_INSTRUCTIONS.md`
3. Write commit message: "Add database sync scripts for local to production"
4. Click **"Commit to main"**
5. Click **"Push origin"**

---

#### Step 1.2: Deploy to Production

1. Go to **Hostinger Panel** → **GIT section**
2. Find `insurancev2_frontend` repository
3. Click **"Deploy"** button
4. Wait for "Deployment end" message

---

#### Step 1.3: Run Export on Local

1. **Start local server** (if not running):
   ```bash
   cd /Users/rajesh/Documents/GitHub/insurancev2_frontend
   php artisan serve
   ```

2. **Open browser** and visit:
   ```
   http://127.0.0.1:8000/SYNC_EXPORT_LOCAL.php
   ```

3. **You'll see:**
   - ✅ Found 559 policies
   - ✅ Found 0-X policy versions
   - ✅ Found 2 agents
   - ✅ Export completed successfully
   - File: `local_to_production.sql` created

4. **Verify file exists:**
   - Check your project folder
   - File: `insurancev2_frontend/local_to_production.sql`
   - Size: Should be 500KB - 2MB

---

### PHASE 2: UPLOAD TO PRODUCTION (2 minutes)

#### Step 2.1: Commit SQL File

1. **GitHub Desktop** will now show:
   - New file: `local_to_production.sql`
2. **Commit with message:** "Add local database export for production sync"
3. Click **"Push origin"**

---

#### Step 2.2: Deploy Again

1. Go to **Hostinger Panel** → **GIT section**
2. Click **"Deploy"** for `insurancev2_frontend`
3. Wait for "Deployment end"
4. **The SQL file is now on production!**

---

### PHASE 3: IMPORT ON PRODUCTION (5 minutes)

#### Step 3.1: Run Import Script

1. **Open browser** and visit:
   ```
   https://v2insurance.softpromis.com/SYNC_IMPORT_PRODUCTION.php
   ```

2. **You'll see progress:**
   - 🗑️ Cleared table: renewals
   - 🗑️ Cleared table: policies
   - 🗑️ Cleared table: policy_versions
   - 🗑️ Cleared table: agents
   - ✅ Executed XXX SQL statements
   - ✅ Import completed successfully

3. **Verify counts:**
   - Policies: 559
   - Policy Versions: 0 (or whatever local had)
   - Agents: 2
   - Renewals: 0 ✅ (cleared!)

---

### PHASE 4: VERIFY & CLEANUP (5 minutes)

#### Step 4.1: Clear Production Cache

Visit:
```
https://v2insurance.softpromis.com/clear-all-cache-now
```

---

#### Step 4.2: Test Production

1. **Quit browser** (Cmd+Q)
2. **Restart browser**
3. **Login to:** `https://v2insurance.softpromis.com`
4. **Hard refresh:** Cmd+Shift+R

**Test Renewals Page:**
1. Go to **Renewals** page
2. **Should show:**
   - Status: "PENDING" or "IN PROGRESS" (NOT "RENEWED" ✅)
   - Pending Renewals: 54 (policies expiring in 30 days)
   - Completed Renewals: 0 ✅

**Test Policy Type Filter:**
1. Go to **Policies** page
2. Select **"Health"** from Policy Type dropdown
3. **Counts should update:**
   - Active Policies: ~14
   - Total Policies: ~14
4. Select **"Motor"** → Counts update to ~545
5. Select **"All"** → Shows all 559

---

#### Step 4.3: Delete Sync Files (SECURITY!)

**Via Hostinger File Manager:**

1. Navigate to `/public_html/v2insurance/`
2. **Delete these files:**
   - `SYNC_EXPORT_LOCAL.php`
   - `SYNC_IMPORT_PRODUCTION.php`
   - `local_to_production.sql`

**Or commit deletion via GitHub:**

1. In your local project, delete:
   - `SYNC_EXPORT_LOCAL.php`
   - `SYNC_IMPORT_PRODUCTION.php`
   - `local_to_production.sql`
2. Commit: "Remove sync scripts after successful sync"
3. Push to GitHub
4. Deploy on Hostinger

---

## EXPECTED RESULTS

### Before Sync (Production):
- Renewals page shows "RENEWED" status ❌
- Old renewal tracking system ❌
- Mismatched with local ❌

### After Sync (Production):
- Renewals page shows "PENDING"/"IN PROGRESS" status ✅
- Matches local exactly ✅
- New pending status logic (0-30 days) ✅
- Policy Type filter working ✅
- No "RENEWED" status anywhere ✅

---

## TROUBLESHOOTING

### Error: "SQL file not found"
**Solution:** Make sure you deployed the SQL file to production via GitHub Desktop

### Error: "Table doesn't exist"
**Solution:** Run migrations first: `/run-migrations-now`

### Import takes too long
**Solution:** Normal for 559 policies. Wait up to 2-3 minutes.

### Still seeing "RENEWED" status
**Solution:** 
1. Clear cache: `/clear-all-cache-now`
2. Quit browser (Cmd+Q)
3. Restart and hard refresh (Cmd+Shift+R)

---

## ROLLBACK (If Something Goes Wrong)

If you need to restore production:

1. You have `production.sql` as backup
2. Upload it to production
3. Import via phpMyAdmin or create a restore script

---

## SUPPORT

If you encounter any issues:
1. Take screenshot of error message
2. Check Laravel logs: `storage/logs/laravel.log`
3. Note which step failed
4. We can troubleshoot from there

---

**Created:** Nov 5, 2025
**Last Updated:** Nov 5, 2025

