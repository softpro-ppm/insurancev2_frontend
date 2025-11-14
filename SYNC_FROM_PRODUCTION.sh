#!/bin/bash

# ========================================
# Production to Local Sync Script
# ========================================

echo ""
echo "╔════════════════════════════════════════╗"
echo "║  PRODUCTION → LOCAL SYNC TOOL          ║"
echo "╚════════════════════════════════════════╝"
echo ""

# Server details
SERVER="u820431346@145.14.146.15"
PORT="65002"
REMOTE_PATH="~/public_html/v2insurance"
LOCAL_PATH="/Users/rajesh/Documents/GitHub/insurancev2_frontend"

# Step 1: Download database export
echo "📥 Step 1/3: Downloading database export..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
scp -P $PORT $SERVER:$REMOTE_PATH/policies_export.json $LOCAL_PATH/

if [ $? -eq 0 ]; then
    echo "✅ Database export downloaded successfully!"
    
    # Check file size
    FILE_SIZE=$(du -h "$LOCAL_PATH/policies_export.json" | cut -f1)
    echo "   File size: $FILE_SIZE"
else
    echo "❌ Failed to download database export"
    echo "   Make sure you ran the export command on the server first!"
    exit 1
fi

echo ""
echo "📥 Step 2/3: Downloading documents (this may take 10-30 minutes)..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "   Press ENTER to continue or Ctrl+C to skip..."
read -r

# Step 2: Download documents
rsync -avz --progress -e "ssh -p $PORT" \
  $SERVER:$REMOTE_PATH/storage/app/private/ \
  $LOCAL_PATH/storage/app/private/

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Documents downloaded successfully!"
else
    echo ""
    echo "⚠️  Documents sync encountered issues (this is OK, continue anyway)"
fi

echo ""
echo "💾 Step 3/3: Importing to local database..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "   This will DELETE your local policies and import production data"
echo "   Press ENTER to continue or Ctrl+C to cancel..."
read -r

# Step 3: Import to local database
cd $LOCAL_PATH
php import_policies_local.php

echo ""
echo "╔════════════════════════════════════════╗"
echo "║  SYNC COMPLETED!                       ║"
echo "╚════════════════════════════════════════╝"
echo ""
echo "🧹 Cleanup: Delete the export file from production server for security!"
echo "   Run: ssh -p $PORT $SERVER 'rm $REMOTE_PATH/policies_export.json'"
echo ""

