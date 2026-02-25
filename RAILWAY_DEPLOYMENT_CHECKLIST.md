# ✅ RAILWAY DEPLOYMENT FINAL CHECKLIST

## Pre-Deployment (Local)

### Code Preparation

- [ ] All code committed to git
- [ ] No uncommitted changes
- [ ] `.env` file NOT committed (only `.env.example`)
- [ ] `composer.lock` committed
- [ ] `package-lock.json` committed

```bash
git status  # Should be clean
```

### Build & Optimization

- [ ] Frontend built: `npm run build`
- [ ] All dependencies installed: `composer install`
- [ ] No errors in build output
- [ ] Migration files exist: `database/migrations/`
- [ ] Seeder files exist (optional): `database/seeders/`

```bash
npm run build        # Should succeed
php artisan migrate:status  # Check migrations
```

### Security Review

- [ ] APP_DEBUG = false ✅
- [ ] APP_ENV = production ✅
- [ ] APP_KEY is set ✅
- [ ] No hardcoded secrets in code ✅
- [ ] Cloudinary credentials secure ✅
- [ ] reCAPTCHA keys for production ✅
- [ ] Database password strong ✅

```bash
grep -r "password" app/  # Should not find hardcoded passwords
```

### Configuration

- [ ] `.env.example` updated with production values
- [ ] Dockerfile exists ✅
- [ ] Procfile exists ✅
- [ ] `.railwayignore` exists ✅
- [ ] `railway-deploy.sh` ready ✅

---

## Railway Setup (Dashboard)

### 1. Account Setup

- [ ] Railway account created: https://railway.app
- [ ] Email verified
- [ ] Payment method added (if needed)

### 2. Project Creation

- [ ] New project created in Railway
- [ ] GitHub repository connected
- [ ] Branch selected: `main` (or your branch)

### 3. Environment Variables

- [ ] All variables from `.env` added in Railway:

```env
APP_NAME=BukuTamuKP
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://your-app.up.railway.app

DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=...

LOG_CHANNEL=stderr
LOG_LEVEL=error
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME=...

SUPABASE_URL=...
SUPABASE_ANON_KEY=...

CLOUDINARY_CLOUD_NAME=...
CLOUDINARY_API_KEY=...
CLOUDINARY_API_SECRET=...

RECAPTCHA_SITE_KEY=...
RECAPTCHA_SECRET_KEY=...
```

- [ ] Variables verified (no typos)
- [ ] Sensitive values not shared

### 4. Build & Deploy

- [ ] Dockerfile auto-detected ✅
- [ ] Deploy triggered ✅
- [ ] Logs monitored: `railway logs -f` ✅

---

## Post-Deployment Verification

### Build Status

- [ ] Build succeeded (no errors)
- [ ] Deployment succeeded
- [ ] App is running (green status)

### Test Endpoints

- [ ] Health check: `https://your-app.up.railway.app/health` → 200 OK ✅
- [ ] Login page: `https://your-app.up.railway.app/resepsionis/login` → Loads ✅
- [ ] Tamu form: `https://your-app.up.railway.app/` → Loads ✅

### Database Verification

```bash
railway shell
php artisan migrate:status  # All migrations ✅
php artisan tinker          # DB::connection()->getPdo() works
```

### Feature Testing

- [ ] Login page loads
- [ ] Form submission works
- [ ] Images upload (KTP)
- [ ] Email sends (if configured)
- [ ] reCAPTCHA validates
- [ ] Pagination works

```bash
# Test in Railway shell
railway run php artisan tinker
>>> \App\Models\Tamu::count()
>>> \App\Models\Kunjungan::first()
```

### Performance Monitoring

- [ ] App responds in <1 second
- [ ] X-Response-Time header present
- [ ] Memory usage acceptable (< 512MB)
- [ ] No errors in logs

```bash
railway logs -f
# Look for: ERROR, CRITICAL only
# Should see: INFO messages, not errors
```

---

## Continuous Monitoring (After Deploy)

### Daily Checks

- [ ] App is online: `https://your-app.up.railway.app/health`
- [ ] No errors in logs
- [ ] Database connection stable

```bash
railway logs -f | grep ERROR
```

### Weekly Checks

- [ ] User reports no issues
- [ ] Performance metrics good
- [ ] Backups created (Railway auto-backups)

```bash
railway status
```

### Monthly Checks

- [ ] Update dependencies
- [ ] Review error logs
- [ ] Check security patches
- [ ] Monitor costs

---

## Common Issues & Fixes

### ❌ Build Failed

```bash
# Check logs
railway logs -f | grep -A 10 "ERROR"

# Fix:
git push origin main  # Retry deployment
railway redeploy
```

### ❌ Database Migration Failed

```bash
# Run manually
railway shell
php artisan migrate:fresh --seed

# Or check status
php artisan migrate:status
```

### ❌ 502 Bad Gateway

```bash
# Usually means app crashed
railway logs -f

# Restart app
railway redeploy

# Check memory limit
railway variables  # Look for PHP_MEMORY_LIMIT
```

### ❌ Images Not Loading

```bash
# Check Cloudinary credentials
railway variables | grep CLOUDINARY

# Verify in tinker
railway run php artisan tinker
>>> config('cloudinary.cloud_name')  # Should return your cloud name
```

### ❌ Email Not Sending

```bash
# Verify credentials
railway variables | grep MAIL_

# Test send
railway run php artisan tinker
>>> Mail::to('test@example.com')->send(new TestMail())
```

---

## Rollback Procedures

If deployment breaks production:

### Option 1: Redeploy Previous Version

```bash
# Revert git
git revert HEAD
git push origin main

# Railway auto-redeploys
# (wait 5-10 minutes)
```

### Option 2: Manual Rollback

```bash
railway redeploy --deployment=<previous-deployment-id>
```

Find deployment ID in Railway Dashboard → Deployments

---

## Performance Optimization (Post-Deploy)

After 24 hours of operation:

- [ ] Check slow requests
- [ ] Optimize queries if needed
- [ ] Enable caching if not already
- [ ] Monitor memory usage
- [ ] Review error logs

```bash
# View metrics
railway logs -f | grep "Slow request"

# Check database
railway run php artisan tinker
>>> DB::enableQueryLog()
>>> \App\Models\Kunjungan::with('tamu')->get()
>>> dd(DB::getQueryLog())
```

---

## Maintenance Schedule

### Weekly

- Review logs
- Check performance
- Monitor errors

### Monthly

- Update dependencies: `composer update`
- Security patches
- Backup check

### Quarterly

- Code review
- Performance audit
- Security audit

---

## Emergency Contacts

| Issue                | Action                         |
| -------------------- | ------------------------------ |
| App down             | Check Railway Dashboard → Logs |
| Database issue       | Check Supabase Dashboard       |
| Email not sending    | Check Gmail account activity   |
| Image upload failing | Check Cloudinary dashboard     |
| Performance issue    | Check `X-Response-Time` header |

---

## Success Criteria

✅ **Deployment Successful When:**

- [ ] App loads without errors
- [ ] Health check returns 200 OK
- [ ] Database connected
- [ ] All features work
- [ ] No error logs
- [ ] Response time < 1 second
- [ ] SSL certificate valid
- [ ] Backups working

---

## Post-Launch

1. **Announce to users**
    - Share new URL
    - Test all features
    - Gather feedback

2. **Monitor for 48 hours**
    - Watch logs
    - Test features
    - Check performance

3. **Setup alerts** (optional)
    - Email on errors
    - Slack notifications
    - Performance monitoring

4. **Document**
    - Add to team wiki
    - Share deployment guide
    - Document architecture

---

## 🎉 DEPLOYMENT COMPLETE!

Your app is now live in production!

**Share your URL:**

```
https://buku-tamu-kp.up.railway.app
```

**Monitor with:**

```bash
railway logs -f
```

**Good luck!** 🚀
