# 🚨 URGENT: Fix Agent Login 404 Errors on Hostinger

## ❌ Current Problem:
- Agent login page shows 404 error
- Agent dashboard shows 404 error
- Only admin user exists in database

## ✅ Solution: Complete Deployment

### 📁 Files to Upload to Hostinger:

**CRITICAL - Upload these files immediately:**

1. **Controllers:**
   - `app/Http/Controllers/Auth/AgentAuthenticatedSessionController.php`
   - `app/Http/Controllers/AgentDashboardController.php`

2. **Middleware:**
   - `app/Http/Middleware/AgentAuth.php`

3. **Models:**
   - `app/Models/Agent.php`

4. **Views:**
   - `resources/views/auth/agent-login.blade.php`
   - `resources/views/layouts/agent.blade.php`
   - `resources/views/agent/dashboard.blade.php`

5. **Configuration:**
   - `config/auth.php`
   - `bootstrap/app.php`
   - `routes/web.php`

### 🗄️ Database Setup:

**Run this SQL in phpMyAdmin:**

```sql
-- Create agents table if it doesn't exist
CREATE TABLE IF NOT EXISTS `agents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'Active',
  `policies_count` int(11) DEFAULT 0,
  `performance` decimal(8,2) DEFAULT 0.00,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agents_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create agent user
INSERT INTO agents (name, phone, email, user_id, status, policies_count, performance, address, password, created_at, updated_at) VALUES (
    'Chinta Balaram Naidu',
    '+919876543210',
    'chbalaram321@gmail.com',
    'AG001',
    'Active',
    0,
    0.00,
    'Hyderabad, Telangana',
    '$2y$12$IyxmNN8ICbf3q6NUIvqQgO/wuoTjzpqTeh9r1DjTAuBM6yV0ykRA.',
    NOW(),
    NOW()
);
```

### 🚀 Deployment Steps:

1. **Upload Files:**
   - Upload all files listed above to Hostinger
   - Replace existing files in same locations

2. **Run SQL:**
   - Go to phpMyAdmin
   - Run the SQL above to create agent user

3. **Test:**
   - Go to: `https://v2insurance.softpromis.com/agent/login`
   - Login with: `chbalaram321@gmail.com` / `agent123`

### 🎯 Expected Result:

- ✅ Agent login page loads (no more 404)
- ✅ Agent dashboard loads (no more 404)
- ✅ Agent can log in successfully
- ✅ Agent dashboard shows data

### 🔧 If Still Getting 404:

1. **Check .htaccess:**
   ```apache
   RewriteEngine On
   RewriteRule ^(.*)$ public/index.php [QSA,L]
   ```

2. **Check Laravel Routes:**
   ```bash
   php artisan route:list | grep agent
   ```

3. **Clear Cache:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

## 🎉 After Deployment:

**Agent Login URL:** `https://v2insurance.softpromis.com/agent/login`  
**Agent Dashboard URL:** `https://v2insurance.softpromis.com/agent/dashboard`  
**Credentials:** `chbalaram321@gmail.com` / `agent123`

**The 404 errors will be fixed once you upload these files!** 🚀
