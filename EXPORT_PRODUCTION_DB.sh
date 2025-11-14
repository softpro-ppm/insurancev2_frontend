#!/bin/bash
echo "🔐 Please enter your SSH password when prompted"
echo ""

# Get production database credentials
ssh -p 65002 u820431346@145.14.146.15 "cd ~/public_html/v2insurance && cat .env | grep DB_"
