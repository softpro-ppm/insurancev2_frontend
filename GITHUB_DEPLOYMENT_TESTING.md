# GitHub Deployment Testing Guide

## Current Setup Analysis
- **Repository**: `https://github.com/softpro-ppm/insurancev2_frontend.git`
- **Branch**: `main`
- **Status**: Up to date with origin/main
- **Pending Changes**: `deployment_package/config/app.php` (modified)
- **No GitHub Actions**: Currently no automated deployment workflows

## Method 1: Test Git Push to GitHub

### Step 1: Commit and Push Current Changes
```bash
# Add the modified file
git add deployment_package/config/app.php

# Commit with a descriptive message
git commit -m "Fix encryption cipher configuration for production deployment"

# Push to GitHub
git push origin main
```

### Step 2: Verify on GitHub
1. Go to: https://github.com/softpro-ppm/insurancev2_frontend
2. Check if your latest commit appears
3. Verify the file changes are reflected
4. Check commit history and timestamps

## Method 2: Test with a Simple Test File

### Create a test file to verify deployment
```bash
# Create a test file
echo "Deployment test - $(date)" > DEPLOYMENT_TEST.txt

# Add, commit, and push
git add DEPLOYMENT_TEST.txt
git commit -m "Test deployment - $(date '+%Y-%m-%d %H:%M:%S')"
git push origin main
```

## Method 3: Manual Deployment Testing

### Option A: Download from GitHub (Manual)
1. Go to your GitHub repository
2. Click "Code" → "Download ZIP"
3. Extract and upload to your Hostinger server
4. Test the application

### Option B: Git Clone on Server (if SSH available)
```bash
# On your server (if you have SSH access)
git clone https://github.com/softpro-ppm/insurancev2_frontend.git
cd insurancev2_frontend
```

## Method 4: Set Up Automated Deployment (Recommended)

### Create GitHub Actions Workflow
Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]
  workflow_dispatch:

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_sqlite
        
    - name: Install Dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Create deployment package
      run: |
        mkdir -p deploy
        cp -r app deploy/
        cp -r config deploy/
        cp -r database deploy/
        cp -r public deploy/
        cp -r resources deploy/
        cp -r routes deploy/
        cp -r vendor deploy/
        cp artisan deploy/
        cp composer.json deploy/
        cp composer.lock deploy/
        
    - name: Deploy to server via FTP
      uses: SamKirkland/FTP-Deploy-Action@4.3.3
      with:
        server: ${{ secrets.FTP_SERVER }}
        username: ${{ secrets.FTP_USERNAME }}
        password: ${{ secrets.FTP_PASSWORD }}
        local-dir: deploy/
        server-dir: /domains/v2.insurance.softpromis.com/public_html/
```

## Method 5: Testing Deployment Success

### 1. File-based Test
Create a version file to track deployments:

```php
<?php
// version.php - Create this file
return [
    'version' => '2.0.1',
    'deployed_at' => '2024-01-XX XX:XX:XX',
    'commit' => 'f3f9c26',
    'environment' => 'production'
];
```

### 2. Health Check Endpoint
Add a simple health check route in `routes/web.php`:

```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'timestamp' => now(),
        'version' => '2.0.1',
        'database' => DB::connection()->getPdo() ? 'Connected' : 'Failed'
    ]);
});
```

### 3. Test URLs to Check
After deployment, test these URLs:
- https://v2.insurance.softpromis.com/ (Main app)
- https://v2.insurance.softpromis.com/health (Health check)
- https://v2.insurance.softpromis.com/login (Login page)

## Quick Deployment Test Commands

```bash
# 1. Test git connectivity
git remote -v

# 2. Check current status
git status

# 3. Add deployment test file
echo "Test deployment $(date)" > deployment-test.txt
git add deployment-test.txt
git commit -m "Test deployment $(date)"
git push origin main

# 4. Verify on GitHub
# Visit: https://github.com/softpro-ppm/insurancev2_frontend/commits/main

# 5. Clean up test file
git rm deployment-test.txt
git commit -m "Remove deployment test file"
git push origin main
```

## Troubleshooting

### If push fails:
```bash
# Pull latest changes first
git pull origin main

# Then push
git push origin main
```

### If authentication fails:
```bash
# Configure credentials
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# Or use personal access token
git remote set-url origin https://YOUR_TOKEN@github.com/softpro-ppm/insurancev2_frontend.git
```

## Next Steps

1. **Immediate**: Test git push with current changes
2. **Short-term**: Set up automated deployment with GitHub Actions
3. **Long-term**: Implement proper CI/CD pipeline with testing

## Monitoring Deployment Success

### Signs of successful deployment:
- ✅ Commits appear on GitHub
- ✅ Website loads without errors
- ✅ Database connections work
- ✅ Authentication functions properly
- ✅ No 500/encryption errors

### Signs of failed deployment:
- ❌ 500 Internal Server Error
- ❌ White screen of death
- ❌ Database connection errors
- ❌ Missing files/assets
- ❌ Encryption key errors
