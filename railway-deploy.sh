#!/bin/bash

# 🚀 RAILWAY DEPLOYMENT SCRIPT
# This script helps you deploy to Railway

set -e

echo "🚀 Starting Railway Deployment..."

# 1. Check if Railway CLI is installed
if ! command -v railway &> /dev/null; then
    echo "❌ Railway CLI not found. Installing..."
    npm install -g @railway/cli
fi

# 2. Login to Railway
echo "📝 Logging in to Railway..."
railway login

# 3. Create new project (or link existing)
echo "🆕 Creating/Linking Railway project..."
railway init

# 4. Add services
echo "🔗 Configuring services..."
# Database (PostgreSQL) - will be provided by Supabase URL in .env
# We'll use the DB_URL from environment variables

# 5. Set environment variables
echo "⚙️  Setting environment variables..."
railway variables set \
  APP_ENV=production \
  APP_DEBUG=false \
  APP_KEY=base64:iSlOfqnlIK/75t4IdLXWNPqq1LIXuUb+IwKm5zeqpE4= \
  LOG_LEVEL=error \
  LOG_CHANNEL=stderr

# 6. Deploy
echo "🚀 Deploying to Railway..."
railway up

echo "✅ Deployment complete!"
echo "📊 View your app at: railway status"
