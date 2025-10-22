# Production Deployment Guide - Document Download Fix

## 🎯 **Issue:**
Your production server at `v2insurance.softpromis.com` is still generating "Document Not Available" placeholder PDFs instead of downloading actual policy documents.

## 🔧 **Solution:**
Deploy the updated `PolicyController.php` file to your Hostinger production server.

## 📋 **Files to Deploy:**

### 1. **Primary Fix File:**
- `app/Http/Controllers/PolicyController.php` - Contains the document download fixes

### 2. **Supporting Files:**
- `app/Models/PolicyVersion.php` - Version management improvements
- `routes/web.php` - Updated routes
- `routes/auth.php` - Updated authentication routes

## 🚀 **Deployment Steps:**

### **Option 1: Manual File Upload (Recommended)**

1. **Access your Hostinger File Manager** or use FTP/SFTP
2. **Navigate to your website directory** (usually `public_html` or similar)
3. **Upload the updated files:**
   - Upload `app/Http/Controllers/PolicyController.php`
   - Upload `app/Models/PolicyVersion.php`
   - Upload `routes/web.php`
   - Upload `routes/auth.php`

### **Option 2: Git Deployment (If using Git)**

1. **Commit your local changes:**
   ```bash
   git add .
   git commit -m "Fix document download issues"
   git push origin main
   ```

2. **Pull changes on production server:**
   ```bash
   git pull origin main
   ```

### **Option 3: Direct Server Edit**

1. **Access your production server** via SSH or File Manager
2. **Edit the files directly** with the updated code
3. **Save the changes**

## 🔍 **Key Changes Made:**

### **Document Download Logic:**
```php
// Handle different types of file paths
$fullPath = null;
$isRemoteUrl = false;

// Check if it's a URL (starts with http/https)
if (str_starts_with($filePath, 'http://') || str_starts_with($filePath, 'https://')) {
    $isRemoteUrl = true;
    $fullPath = $filePath;
} else {
    // Try multiple possible local storage paths
    $possiblePaths = [
        storage_path('app/' . $filePath),
        storage_path('app/public/' . $filePath),
        public_path('storage/' . $filePath),
        public_path('uploads/' . $filePath),
        storage_path($filePath),
        $filePath // Direct path
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $fullPath = $path;
            break;
        }
    }
}

// Handle remote URLs vs local files
if ($isRemoteUrl) {
    // For remote URLs, redirect to the URL
    return redirect($fullPath);
} else {
    // For local files, use download response
    return response()->download($fullPath, $friendlyName);
}
```

### **Version Document Preservation:**
```php
private function preserveDocumentsForVersion(Policy $policy, PolicyVersion $version)
{
    $documentFields = ['policy_copy_path', 'rc_copy_path', 'aadhar_copy_path', 'pan_copy_path'];
    
    foreach ($documentFields as $field) {
        $currentPath = $policy->$field;
        if ($currentPath) {
            // Store the current document path in the version
            $version->update([$field => $currentPath]);
        }
    }
}
```

## ✅ **After Deployment:**

1. **Clear Production Cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. **Test Document Downloads:**
   - Go to your production dashboard
   - Open a policy
   - Click "View History"
   - Try downloading a document
   - Should now download actual documents instead of placeholder PDFs

## 🎯 **Expected Results:**

- ✅ **No more "Document Not Available" PDFs**
- ✅ **Actual policy documents download**
- ✅ **Version history shows real documents**
- ✅ **Both local and remote files work**

## 📞 **If Issues Persist:**

1. **Check file permissions** on production server
2. **Verify document paths** in your database
3. **Check server logs** for any errors
4. **Ensure all files were uploaded correctly**

The fixes are ready - you just need to deploy them to production! 🚀
