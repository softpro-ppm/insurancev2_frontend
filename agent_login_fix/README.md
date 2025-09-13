# 🚨 URGENT: Fix Agent Login 404 Errors

## ❌ Problem:
- `https://v2insurance.softpromis.com/agent/login` → 404 Error
- `https://v2insurance.softpromis.com/agent/dashboard` → 404 Error
- Only admin user exists in database

## ✅ Solution:

### 📁 Step 1: Upload Files to Hostinger

Upload these files from the `agent_login_fix` folder to Hostinger:

**File Mapping:**
```
agent_login_fix/AgentAuthenticatedSessionController.php → app/Http/Controllers/Auth/
agent_login_fix/AgentDashboardController.php → app/Http/Controllers/
agent_login_fix/AgentAuth.php → app/Http/Middleware/
agent_login_fix/Agent.php → app/Models/
agent_login_fix/agent-login.blade.php → resources/views/auth/
agent_login_fix/agent.blade.php → resources/views/layouts/
agent_login_fix/dashboard.blade.php → resources/views/agent/
agent_login_fix/auth.php → config/
agent_login_fix/app.php → bootstrap/
agent_login_fix/web.php → routes/
```

### 🗄️ Step 2: Create Agent in Database

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

### 🧪 Step 3: Test

1. **Agent Login:** `https://v2insurance.softpromis.com/agent/login`
2. **Credentials:** `chbalaram321@gmail.com` / `agent123`
3. **Expected:** Redirect to agent dashboard

### 🎯 Expected Result:

- ✅ No more 404 errors
- ✅ Agent login page loads
- ✅ Agent dashboard loads
- ✅ Agent can log in successfully

## 🔧 Quick Fix Commands (if you have SSH access):

```bash
# Clear Laravel cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check routes
php artisan route:list | grep agent
```

## 📞 If Still Not Working:

1. **Check .htaccess** in public folder
2. **Verify file permissions** (644 for files, 755 for directories)
3. **Check Laravel logs** in storage/logs/
4. **Ensure web server** is pointing to public/ directory

**The 404 errors will be fixed once you upload these files!** 🚀
