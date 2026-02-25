# 🚀 RAILWAY DEPLOYMENT - COMPLETE GUIDE

## WHAT I PREPARED FOR YOU

I sudah menyiapkan **semua yang Anda butuhkan** untuk deploy ke Railway:

### ✅ Files Created/Updated

```
✨ NEW FILES:
├─ Dockerfile              (Production Docker image)
├─ Procfile               (Deployment configuration)
├─ .railwayignore        (Ignore patterns for Railway)
├─ railway-deploy.sh     (Deployment script)
├─ app/Http/Controllers/HealthCheckController.php (Health monitoring)
│
📖 DOCUMENTATION:
├─ RAILWAY_DEPLOYMENT.md              (Full guide, 200+ lines)
├─ RAILWAY_QUICK_START.md            (5-minute quick start)
├─ RAILWAY_DEPLOYMENT_CHECKLIST.md   (Step-by-step checklist)
│
✏️  MODIFIED:
└─ routes/web.php                     (Added health check routes)
```

---

## QUICK START (Choose One)

### 🟢 Option A: Dashboard (Easiest - 5 minutes)

1. **Go to:** https://railway.app
2. **Click:** "New Project" → "Deploy from GitHub"
3. **Connect:** Select your repo
4. **Add Variables:** Copy from RAILWAY_DEPLOYMENT.md
5. **Deploy!** Railway auto-builds

### 🔵 Option B: CLI (Recommended - 10 minutes)

```powershell
# 1. Install Railway CLI
npm install -g @railway/cli

# 2. Build locally first
npm run build

# 3. Login & Deploy
railway login
railway init
railway up

# 4. View logs
railway logs -f
```

---

## REQUIRED ENVIRONMENT VARIABLES

Copy these to Railway Dashboard → Variables:

```env
APP_NAME=BukuTamuKP
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:iSlOfqnlIK/75t4IdLXWNPqq1LIXuUb+IwKm5zeqpE4=
APP_URL=https://your-app-name.up.railway.app

DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=ijazahpalsuwok

LOG_CHANNEL=stderr
LOG_LEVEL=error
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=aplikasibukutamukp@gmail.com
MAIL_PASSWORD=inoovhejhysgmdyk
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=aplikasibukutamukp@gmail.com
MAIL_FROM_NAME="Aplikasi Buku Tamu"

SUPABASE_URL=https://durahjthcklgttsnxbvf.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImR1cmFoanRoY2tsZ3R0c254YnZmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Njc4Mzk0NzAsImV4cCI6MjA4MzQxNTQ3MH0.1t1y7iBZ_9Jf7v0cxE2ivyRR1MN_ntEjPGO5kYkEloE

CLOUDINARY_CLOUD_NAME=dfd6zgzxd
CLOUDINARY_API_KEY=319465468272661
CLOUDINARY_API_SECRET=UgP_juoE30R1jYZB1DsVNAdjhIM

RECAPTCHA_SITE_KEY=6LfKWlYsAAAAAA2C10y6gKLweH_QBbFr89YCjz4n
RECAPTCHA_SECRET_KEY=6LfKWlYsAAAAAOyq1PiP5Dwek3-77dn1gtCAt-WF
```

**✅ Update APP_URL after deploy!**

---

## TESTING AFTER DEPLOY

Once Railway shows your app is live:

### Test Health Checks

```bash
# Should return 200 OK
curl https://your-app.up.railway.app/health
curl https://your-app.up.railway.app/health/ready
curl https://your-app.up.railway.app/health/live
```

### Test Database

```bash
railway shell
php artisan migrate:status
php artisan tinker
>>> DB::connection()->getPdo()  # Should work
```

### Test Features

- [ ] Open login page
- [ ] Try login
- [ ] Check form
- [ ] Upload image
- [ ] Send form

### Monitor Performance

- Open DevTools (F12) → Network tab
- Check response times
- Should be < 1 second per request

---

## WHAT HAPPENS NEXT

### Dockerfile Workflow:

1. Railway gets your code from GitHub
2. Runs Dockerfile commands
3. Installs PHP dependencies (Composer)
4. Installs Node dependencies (npm)
5. Builds frontend assets (npm run build)
6. Creates production image
7. Runs migrations (`php artisan migrate --force`)
8. Starts app on port 8000
9. Exposes on https://your-app.up.railway.app

### Auto-Deploy:

Every time you push to GitHub:

```bash
git push origin main
# 🚀 Railway automatically redeploys!
```

---

## TROUBLESHOOTING

### ❌ Build Failed

```bash
# Check logs in Railway Dashboard
# Or via CLI:
railway logs -f

# Common causes:
# - Missing dependencies in composer.json
# - Node/npm version mismatch
# - Memory limit exceeded
```

### ❌ App Won't Start

```bash
# Check if port 8000 is open (Railway uses dynamic port)
# Dockerfile already configures this

# Run manually:
railway shell
php artisan serve --host=0.0.0.0 --port=8000
```

### ❌ Database Connection Error

```bash
# Verify DATABASE_URL is correct
# Format: postgresql://user:password@host:port/database

# Test connection:
railway run php artisan tinker
>>> DB::connection()->getPdo()
```

### ❌ Assets Not Loading (404)

```bash
# Rebuild frontend locally
npm run build

# Commit and push
git add public/build/
git commit -m "Update assets"
git push

# Railway redeploys automatically
```

---

## MONITORING

### View Logs

```bash
# Live logs
railway logs -f

# Last 100 lines
railway logs -n 100

# Last 1 hour
railway logs --since 1h
```

### Check Status

```bash
# App status
railway status

# View environment
railway variables

# View domains
railway domain
```

### Monitor Performance

- Check `X-Response-Time` header in Network tab
- Monitor `/health` endpoint
- Check Railway Dashboard → Metrics

---

## ADVANCED OPTIONS

### Custom Domain

1. Go to Railway Dashboard
2. Settings → Domains
3. Add your domain
4. Update DNS records
5. SSL auto-configured ✅

### CI/CD Pipeline

Railway automatically:

- Deploys on every push
- Runs migrations
- Clears caches
- Rebuilds assets

### Environment-specific Configs

```env
# Local
APP_ENV=local
APP_DEBUG=true

# Production (Railway)
APP_ENV=production
APP_DEBUG=false
```

---

## COST ESTIMATE

Railway pricing (consumption-based):

- **Free tier:** $5/month included
- **Your app:** ~$2-5/month (typical)
- **Database:** Supabase (separate bill, ~$10/month)
- **Total:** Budget ~$15-20/month

No surprise charges - you control spending!

---

## DEPLOYMENT SUMMARY

| Step              | Time          | Status          |
| ----------------- | ------------- | --------------- |
| 1. Prepare files  | ✅ DONE       | All files ready |
| 2. Setup Railway  | 5 min         | Need to do      |
| 3. Configure vars | 5 min         | Need to do      |
| 4. Deploy         | 5-10 min      | Need to do      |
| 5. Test           | 5 min         | Need to do      |
| **Total**         | **20-25 min** | 🟢 Ready        |

---

## NEXT STEPS

1. **Create Railway account** (if not done)
    - https://railway.app
    - Sign up with GitHub

2. **Choose deployment method**
    - Option A: Dashboard (easiest)
    - Option B: CLI (more control)

3. **Add environment variables**
    - Copy from this guide
    - Update APP_URL after deploy

4. **Deploy!**
    - Click deploy in dashboard or `railway up`

5. **Test**
    - Visit `https://your-app.up.railway.app`
    - Check `/health` endpoint
    - Test features

6. **Monitor**
    - Check logs regularly
    - Monitor performance
    - Update when needed

---

## USEFUL COMMANDS

```powershell
# Railway CLI
railway login                    # Login
railway init                     # Initialize
railway up                       # Deploy
railway logs -f                  # View logs
railway shell                    # SSH to app
railway run php artisan tinker   # Run command
railway status                   # Check status
railway variables                # View env vars
railway variables set KEY=VALUE  # Set variable
railway redeploy                 # Redeploy
```

---

## DOCUMENTATION REFERENCE

1. **RAILWAY_DEPLOYMENT.md** - Comprehensive guide (200+ lines)
2. **RAILWAY_QUICK_START.md** - 5-minute quick start
3. **RAILWAY_DEPLOYMENT_CHECKLIST.md** - Pre/post deployment checklist

---

## SUPPORT RESOURCES

- Railway Docs: https://docs.railway.app
- Laravel Docs: https://laravel.com/docs
- Supabase Docs: https://supabase.com/docs
- GitHub Discussions: Your repo discussions

---

## KEY POINTS

✅ **Everything prepared** - All deployment files ready
✅ **Zero configuration** - Just add environment variables
✅ **Auto-deploy** - Every git push auto-redeploys
✅ **Monitoring** - Built-in health checks
✅ **Scalable** - Easy to scale when needed
✅ **Affordable** - ~$15-20/month for full stack

---

## YOU'RE READY! 🚀

All you need to do now:

1. Go to https://railway.app
2. Connect GitHub repo
3. Add environment variables
4. Click Deploy
5. Wait 5-10 minutes
6. Your app is live!

**Enjoy your production deployment!** 🎉

For detailed steps, see **RAILWAY_QUICK_START.md**
