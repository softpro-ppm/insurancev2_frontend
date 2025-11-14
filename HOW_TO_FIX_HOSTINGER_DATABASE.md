# 🔧 How to Fix Hostinger Database (NON-TECHNICAL GUIDE)

## ✅ **The Problem**
Your database is missing some columns. That's why you get the error when adding policies.

## 🎯 **The Solution (5 Minutes)**

### **Step 1: Login to Hostinger**
1. Go to https://hostinger.com
2. Login to your account
3. Go to your **hPanel** (hosting control panel)

### **Step 2: Open phpMyAdmin**
1. In hPanel, find and click **"phpMyAdmin"**
   - It's usually under "Databases" section
2. Click on your database name (on the left side)
   - It's probably named something like `u123456_insurance` or similar

### **Step 3: Run the Fix Script**
1. Click the **"SQL"** tab at the top
2. You'll see a big text box
3. Open the file `FIX_PRODUCTION_DATABASE.sql` from your project
4. Copy ALL the contents
5. Paste it into the SQL text box in phpMyAdmin
6. Click **"Go"** button at the bottom right
7. Wait 5 seconds - you should see "Database columns updated successfully!"

### **Step 4: Test Your Application**
1. Go back to your insurance website
2. Press **Ctrl+Shift+R** (or Cmd+Shift+R on Mac) to refresh
3. Try adding a policy again
4. It should work now! ✅

---

## 📸 **Visual Guide**

### Finding phpMyAdmin in Hostinger:
```
Hostinger hPanel
  └── Databases
      └── phpMyAdmin (click here)
          └── Select your database (left sidebar)
              └── Click "SQL" tab
                  └── Paste the SQL script
                      └── Click "Go"
```

---

## ⚠️ **If You Can't Find phpMyAdmin**

Alternative path:
1. In Hostinger hPanel, go to **"Databases"**
2. Click **"Manage"** next to your database
3. Look for **"Enter phpMyAdmin"** or **"phpMyAdmin"** button

---

## 🆘 **If Script Fails**

If you see errors, try this simpler version instead:

### Simple SQL (Copy & Paste):
```sql
-- Add missing columns to policies table
ALTER TABLE policies ADD COLUMN customer_age SMALLINT UNSIGNED NULL AFTER agent_name;
ALTER TABLE policies ADD COLUMN customer_gender VARCHAR(255) NULL AFTER customer_age;
ALTER TABLE policies ADD COLUMN sum_insured DECIMAL(12, 2) NULL AFTER customer_gender;
ALTER TABLE policies ADD COLUMN sum_assured DECIMAL(12, 2) NULL AFTER sum_insured;
ALTER TABLE policies ADD COLUMN policy_term VARCHAR(255) NULL AFTER sum_assured;
ALTER TABLE policies ADD COLUMN premium_frequency VARCHAR(255) NULL AFTER policy_term;
```

**Note:** If some columns already exist, you might get errors like "Duplicate column" - that's OK! Just ignore them and continue.

---

## ✅ **How to Know It Worked**

After running the script, you should see a table showing all columns in your `policies` table, including the new ones:
- `customer_age`
- `customer_gender`
- `sum_insured`
- `sum_assured`
- `policy_term`
- `premium_frequency`

---

## 🎉 **After This Fix**

Once done:
1. ✅ Motor policies will work
2. ✅ Health policies will work
3. ✅ Life policies will work

All three types should now work without errors!

---

## 📝 **Quick Checklist**

- [ ] Login to Hostinger
- [ ] Open phpMyAdmin
- [ ] Select your database
- [ ] Click "SQL" tab
- [ ] Paste the SQL script
- [ ] Click "Go"
- [ ] See success message
- [ ] Test adding a policy
- [ ] Celebrate! 🎉

---

## ❓ **Need Help?**

If you get stuck:
1. Take a screenshot of the error
2. Send it along with:
   - Which step you're on
   - What you see in phpMyAdmin

---

## 🔒 **Is This Safe?**

Yes! This script:
- ✅ Only **adds** columns, never deletes
- ✅ Checks if columns exist before adding
- ✅ Doesn't touch your existing data
- ✅ Can be run multiple times safely

---

**Time Required:** 5 minutes  
**Technical Skill:** None needed (just copy & paste)  
**Risk Level:** Very low (no data deletion)

