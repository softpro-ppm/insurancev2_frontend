# 🚨 DEBUG: Agent Login 404 Error on Hostinger

## ❌ Current Problem:
- `https://v2insurance.softpromis.com/agent/login` → **404 Error**
- Auto-deployment might not be working
- Files exist locally but not on Hostinger

## 🔍 DEBUG STEPS:

### Step 1: Upload Debug Script
Upload `debug_hostinger.php` to your Hostinger root directory and run it:
- Go to: `https://v2insurance.softpromis.com/debug_hostinger.php`
- This will show exactly what files are missing

### Step 2: Upload Route Test Script
Upload `test_routes.php` to your Hostinger root directory and run it:
- Go to: `https://v2insurance.softpromis.com/test_routes.php`
- This will test which routes are working

## ✅ MANUAL DEPLOYMENT (Guaranteed Fix):

Since auto-deployment might not be working, let's deploy manually:

### 📁 Files to Upload to Hostinger:

**1. Agent Controller:**
```
File: app/Http/Controllers/Auth/AgentAuthenticatedSessionController.php
Upload to: app/Http/Controllers/Auth/AgentAuthenticatedSessionController.php
```

**2. Agent Dashboard Controller:**
```
File: app/Http/Controllers/AgentDashboardController.php
Upload to: app/Http/Controllers/AgentDashboardController.php
```

**3. Agent Middleware:**
```
File: app/Http/Middleware/AgentAuth.php
Upload to: app/Http/Middleware/AgentAuth.php
```

**4. Agent Model:**
```
File: app/Models/Agent.php
Upload to: app/Models/Agent.php
```

**5. Agent Login View:**
```
File: resources/views/auth/agent-login.blade.php
Upload to: resources/views/auth/agent-login.blade.php
```

**6. Agent Layout:**
```
File: resources/views/layouts/agent.blade.php
Upload to: resources/views/layouts/agent.blade.php
```

**7. Agent Dashboard View:**
```
File: resources/views/agent/dashboard.blade.php
Upload to: resources/views/agent/dashboard.blade.php
```

**8. Auth Config:**
```
File: config/auth.php
Upload to: config/auth.php
```

**9. Bootstrap App:**
```
File: bootstrap/app.php
Upload to: bootstrap/app.php
```

**10. Routes:**
```
File: routes/web.php
Upload to: routes/web.php
```

## 🚀 Quick Upload Method:

### Option 1: Download from GitHub
1. Go to: `https://github.com/softpro-ppm/insurancev2_frontend`
2. Click "Code" → "Download ZIP"
3. Extract and upload the files above

### Option 2: Use File Manager
1. Go to Hostinger File Manager
2. Navigate to your project directory
3. Upload each file to the correct location

## 🧪 After Upload - Test:

1. **Debug Script:** `https://v2insurance.softpromis.com/debug_hostinger.php`
2. **Agent Login:** `https://v2insurance.softpromis.com/agent/login`
3. **Login:** `chbalaram321@gmail.com` / `agent123`

## 🔧 If Still 404 After Upload:

1. **Check file permissions** (644 for files, 755 for directories)
2. **Clear Laravel cache** (if SSH access):
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```
3. **Check .htaccess** in public folder
4. **Verify web server** is pointing to public/ directory

## 📞 Why Auto-Deployment Might Not Work:

1. **Hostinger doesn't have GitHub integration**
2. **Auto-deploy is not configured**
3. **Files are in wrong branch**
4. **Permission issues**

**Manual upload is the most reliable method!** 🚀
