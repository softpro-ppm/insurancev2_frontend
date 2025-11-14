# 🚀 DEPLOY & RUN - SIMPLE STEPS

## ✅ **Step 1: Deploy via GitHub Desktop**

You should see these files to commit:
- ✅ `app/Models/PolicyVersion.php`
- ✅ `public/fix_version_documents.php`
- ✅ `fix_version_documents.php`
- ✅ Documentation files

**Commit message:** `Fix policy history downloads + backfill script`

Click **"Push origin"** ✅

---

## ✅ **Step 2: Run the Script in Browser**

After pushing, immediately open your browser and go to:

```
https://v2insurance.softpromis.com/fix_version_documents.php
```

You'll see a **green terminal-style page** showing:
```
🔧 Fix Version Documents Script

📊 Found 15 policy versions to check

🔧 Fixed Version #1 (Policy #998, TANNA THIRUPATHIRAO)
   - Copied 4 document path(s)
   - policy_copy: policy_12345.pdf
   - rc_copy: rc_12345.pdf
   - aadhar_copy: aadhar_12345.pdf
   - pan_copy: pan_12345.pdf

🔧 Fixed Version #2 (Policy #1004, MARISHARLA THIRUPATHIRAO)
   - Copied 4 document path(s)
   - policy_copy: policy_54321.pdf
   ...

📊 Summary
✅ Fixed: 10
⚠️  Skipped: 5
❌ Errors: 0

🎉 SUCCESS!
10 version(s) now have document paths!
```

---

## ✅ **Step 3: Delete the Script (Security)**

After seeing the success message, delete the file:

**Option A: Via File Manager (Hostinger)**
1. Go to File Manager
2. Navigate to `public/` folder
3. Delete `fix_version_documents.php`

**Option B: Via SSH**
```bash
rm public/fix_version_documents.php
```

---

## ✅ **Step 4: Test Your Policy History**

1. Open any policy
2. Click **"History"** button
3. Try downloading documents
4. **Should now download REAL PDFs!** 🎉

---

## 🎯 **What You'll See**

### **Before (What you see now):**
```
Downloads: TANNA_Missing_policy.pdf ❌
Content: "Document Not Available"
```

### **After (What you'll see):**
```
Downloads: TANNA_Version1_policy.pdf ✅
Content: Actual policy document!
```

---

## ⚠️ **Important**

- ✅ Script is now in `public/` folder (web accessible)
- ✅ Will show nice HTML output in browser
- ✅ Safe to run multiple times
- ⚠️ **DELETE after running** (security!)

---

**That's it! 3 steps and your history downloads will work!** 🚀
