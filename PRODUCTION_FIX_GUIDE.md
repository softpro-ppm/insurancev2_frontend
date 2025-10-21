# Production File Storage Fix Guide

## Problem
Users are getting "Document file not found on disk" errors when trying to download policy documents from the policy history modal.

## Root Cause
The database contains references to document files, but the actual PDF files are missing from the server's storage directory. This commonly happens during server migrations or when files are accidentally deleted.

## Solution

### Step 1: Upload Updated Files
Upload these updated files to your production server:
- `app/Http/Controllers/PolicyController.php` (updated with better error handling)
- `diagnose_file_storage.php` (diagnostic tool)
- `fix_missing_documents_production.php` (specific fix for your case)
- `cleanup_missing_documents.php` (cleanup tool)
- `create_placeholder_documents.php` (creates placeholder files)

### Step 2: Run Diagnostic (Optional)
To understand the scope of the problem:
```bash
cd /home/u820431346/domains/softpromis.com/public_html/v2insurance/
php diagnose_file_storage.php
```

### Step 3: Choose Your Fix Strategy

#### Option A: Quick Fix (Recommended)
The updated PolicyController now automatically creates placeholder PDFs for missing documents. Users will download a PDF that says "Document Not Available" instead of getting an error.

**No additional steps needed** - the fix is already in the updated controller!

#### Option B: Create Placeholder Files on Server
If you want to create actual placeholder files on the server:
```bash
cd /home/u820431346/domains/softpromis.com/public_html/v2insurance/
php create_placeholder_documents.php
```

#### Option C: Clean Up Database References
If you want to remove references to missing files entirely:
```bash
cd /home/u820431346/domains/softpromis.com/public_html/v2insurance/
php cleanup_missing_documents.php
```

#### Option D: Restore Missing Files
If you have backups of the missing files, restore them to the storage directory:
```bash
# Example path structure:
/home/u820431346/domains/softpromis.com/public_html/v2insurance/storage/app/private/policies/1436/documents/
```

### Step 4: Test the Fix
1. Go to a policy with missing documents
2. Click on the policy history modal
3. Try to download a document
4. You should now get a placeholder PDF instead of an error

## What the Fix Does

### Enhanced Error Handling
- Tries multiple storage paths before failing
- Provides detailed error logging for administrators
- Creates user-friendly placeholder documents

### Placeholder Documents
When a document is missing, users will download a PDF containing:
- Customer name
- Document type
- "Document Not Available" message
- Contact administrator information
- Timestamp

### Better Logging
All missing file attempts are logged with:
- Policy/Version ID
- Document type
- File path
- All attempted storage locations

## Benefits
✅ No more download errors for users
✅ Clear indication when documents are missing
✅ Detailed logging for administrators
✅ Graceful degradation instead of system errors
✅ Professional user experience

## Monitoring
Check your Laravel logs for entries like:
```
Document not found for policy 1436, tried paths: /path1, /path2, ...
```

This will help you identify which files need to be restored from backup.

## Long-term Solution
1. Implement a backup strategy for document files
2. Set up file integrity monitoring
3. Consider using cloud storage for important documents
4. Regular cleanup of orphaned file references
