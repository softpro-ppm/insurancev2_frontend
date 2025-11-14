#!/bin/bash
# Force update Hostinger server with latest code

echo "🚀 Connecting to Hostinger..."
ssh -p 65002 u820431346@145.14.146.15 << 'ENDSSH'

echo "📂 Going to project directory..."
cd ~/public_html/v2insurance

echo "📥 Pulling latest code from GitHub..."
git pull origin main

echo "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "💾 Caching configuration..."
php artisan config:cache

echo "✅ Server updated successfully!"
echo ""
echo "Current commit:"
git log --oneline -1

echo ""
echo "✅ Done! Now:"
echo "1. Go to your browser"
echo "2. Press Cmd+Shift+R (hard refresh)"
echo "3. The date/time should now display!"

ENDSSH

