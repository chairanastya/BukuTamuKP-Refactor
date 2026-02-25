# 🚀 RAILWAY DEPLOYMENT GUIDE

## Prerequisites

- ✅ Railway account (https://railway.app)
- ✅ Railway CLI installed
- ✅ Git repository
- ✅ Supabase PostgreSQL database (already configured)

---

## STEP-BY-STEP DEPLOYMENT

### 1️⃣ **Install Railway CLI**

```bash
# Install globally
npm install -g @railway/cli

# Or using Homebrew (Mac)
brew install railway

# Verify installation
railway --version
```

---

### 2️⃣ **Login to Railway**

```bash
railway login
```

This will open browser untuk authenticate. Ikuti instruksi di browser.

---

### 3️⃣ **Initialize Railway Project**

```bash
cd d:\Belajar\KAPE\BukuTamuKP-Refactor

# Initialize project
railway init
```

Follow prompts:

- Project name: `BukuTamuKP` (or your choice)
- Region: Choose closest to users (Asia: Singapore/Tokyo)

---

### 4️⃣ **Create railway.json** (Optional but recommended)

Railway automatically detects PHP/Laravel projects. Tapi untuk lebih kontrol:

```json
{
    "build": {
        "builder": "dockerfile"
    },
    "deploy": {
        "startCommand": "php artisan migrate --force && php artisan serve --host 0.0.0.0"
    }
}
```

---

### 5️⃣ **Configure Environment Variables**

Di Railway dashboard:

1. Go to your project → Variables
2. Add these variables:

```env
# App Configuration
APP_NAME=BukuTamuKP
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:iSlOfqnlIK/75t4IdLXWNPqq1LIXuUb+IwKm5zeqpE4=
APP_URL=https://your-app.railway.app (update after deploy)

# Database (from Supabase)
DATABASE_URL=postgresql://postgres.durahjthcklgttsnxbvf:ijazahpalsuwok@aws-1-ap-southeast-1.pooler.supabase.com:5432/postgres

# Or individual vars:
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=ijazahpalsuwok

# Logging
LOG_CHANNEL=stderr
LOG_LEVEL=error

# Cache & Session
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Mail (Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=aplikasibukutamukp@gmail.com
MAIL_PASSWORD=inoovhejhysgmdyk
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=aplikasibukutamukp@gmail.com
MAIL_FROM_NAME=Aplikasi Buku Tamu

# Supabase
SUPABASE_URL=https://durahjthcklgttsnxbvf.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImR1cmFoanRoY2tsZ3R0c254YnZmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Njc4Mzk0NzAsImV4cCI6MjA4MzQxNTQ3MH0.1t1y7iBZ_9Jf7v0cxE2ivyRR1MN_ntEjPGO5kYkEloE

# Cloudinary
CLOUDINARY_CLOUD_NAME=dfd6zgzxd
CLOUDINARY_API_KEY=319465468272661
CLOUDINARY_API_SECRET=UgP_juoE30R1jYZB1DsVNAdjhIM

# reCAPTCHA
RECAPTCHA_SITE_KEY=6LfKWlYsAAAAAA2C10y6gKLweH_QBbFr89YCjz4n
RECAPTCHA_SECRET_KEY=6LfKWlYsAAAAAOyq1PiP5Dwek3-77dn1gtCAt-WF
```

---

### 6️⃣ **Create Dockerfile** (untuk Production-grade setup)

Create `Dockerfile` di root:

```dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    postgresql-client \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Copy .env.example to .env (Railway will inject vars)
RUN cp .env.example .env

# Generate app key
RUN php artisan key:generate

# Clear caches
RUN php artisan config:clear

# Create storage directory
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views}

# Expose port
EXPOSE 8000

# Run migrations and start server
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
```

---

### 7️⃣ **Create Procfile** (Alternative to Dockerfile)

If you prefer simpler setup:

```
web: php -d variables_order=EGPCS artisan serve --host=0.0.0.0 --port=${PORT}
release: php artisan migrate --force
```

---

### 8️⃣ **Update package.json for Build**

Add build script:

```json
{
    "scripts": {
        "build": "npm run build",
        "dev": "vite",
        "postinstall": "npm run build"
    }
}
```

---

### 9️⃣ **Create .railwayignore** (optional)

```
node_modules/
vendor/
.env.local
.git/
storage/logs/*
bootstrap/cache/*
```

---

### 🔟 **Deploy via CLI**

```bash
# Option 1: Direct deploy
railway up

# Option 2: View logs
railway logs -f

# Option 3: Connect to database
railway connect --service postgres

# Option 4: View status
railway status
```

---

## DEPLOYMENT CHECKLIST

```bash
# 1. Commit all changes
git add .
git commit -m "🚀 Prepare for Railway deployment"

# 2. Push to GitHub (Railway can auto-deploy from repo)
git push origin main

# 3. Install Railway CLI
npm install -g @railway/cli

# 4. Login
railway login

# 5. Link project
railway init

# 6. Add variables in Railway dashboard

# 7. Deploy
railway up

# 8. View logs
railway logs -f

# 9. Get app URL
railway status

# 10. Update APP_URL in Railway variables
```

---

## COMMON ISSUES & SOLUTIONS

### ❌ Migration Errors

**Problem**: Migrations fail on deploy

**Solution**:

```bash
# In Railway dashboard, run:
railway run php artisan migrate:refresh --force

# Or use SSH:
railway shell
php artisan migrate --force
exit
```

### ❌ Composer Memory Limit

**Problem**: `Allowed memory size exhausted`

**Solution**: Update Dockerfile

```dockerfile
ENV PHP_MEMORY_LIMIT=512M
RUN php -d memory_limit=512M /usr/bin/composer install
```

### ❌ Database Connection Issues

**Problem**: Can't connect to Supabase

**Solution**:

1. Verify DATABASE_URL is correct
2. Check Supabase connection limits
3. Use railway shell to test:
    ```bash
    railway run php artisan tinker
    >>> DB::connection()->getPdo()
    ```

### ❌ Assets Not Loading (CSS/JS)

**Problem**: Vite assets not compiled

**Solution**:

```bash
# Ensure npm build runs
npm run build

# Commit compiled assets
git add public/build/
git commit -m "Add compiled assets"
```

### ❌ Disk Space Issues

**Problem**: Storage directory permission denied

**Solution**: Use ephemeral storage only

```php
// In config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 's3', // Use Cloudinary instead
        // or use 'public' for read-only
    ]
]
```

---

## CONNECT DATABASE VIA SSH

```bash
# Open shell to your app
railway shell

# Access database
php artisan tinker

# Inside tinker:
>>> DB::select('SELECT * FROM tamuses LIMIT 1')
>>> \App\Models\Tamu::count()
```

---

## VIEW LOGS

```bash
# Stream logs (live)
railway logs -f

# View specific service
railway logs -f --service web

# Download logs
railway logs > app-logs.txt
```

---

## SCALE & PERFORMANCE

```bash
# In Railway Dashboard:
1. Settings → Deployment
2. Set memory: 512MB (default)
3. Set compute: Shared (free tier) or Premium
4. Enable auto-scaling (paid)
```

---

## PRODUCTION OPTIMIZATION

Before deploying:

```bash
# 1. Build frontend
npm run build

# 2. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 3. Optimize autoloader
composer install --optimize-autoloader --no-dev

# 4. Commit everything
git add .
git commit -m "🚀 Production build"

# 5. Deploy
railway up
```

---

## MONITORING

Railway provides:

- ✅ Build logs
- ✅ Deploy logs
- ✅ Application logs (via `railway logs`)
- ✅ Memory/CPU monitoring
- ✅ Custom domain setup

---

## NEXT STEPS AFTER DEPLOY

1. **Set custom domain**
    - Go to Railway dashboard
    - Settings → Domains
    - Add your domain

2. **Setup SSL** (automatic)
    - Railway provides free SSL

3. **Monitor performance**
    - Use `X-Response-Time` headers
    - Check Railway metrics

4. **Setup CI/CD** (auto-deploy on push)
    - Railway auto-deploys from Git
    - Configure branch in settings

---

## USEFUL COMMANDS

```bash
# Redeploy
railway redeploy

# View environment
railway variables

# Set single variable
railway variables set APP_DEBUG=false

# Remove variable
railway variables unset DEBUG

# Connect to shell
railway shell

# Run command
railway run php artisan migrate

# View domains
railway domain

# Stop/Start
railway stop
railway start

# Logs specific timeframe
railway logs --since 1h
railway logs --until 10m
```

---

## PRICING

Railway uses a consumption-based model:

- **Free tier**: $5/month included (enough for small apps)
- **Overages**: $0.50/hour compute + $0.09/GB storage
- No databases included on free tier, but you're using Supabase (external)

---

## SECURITY CHECKLIST

Before production:

```bash
[ ] APP_DEBUG = false ✅
[ ] APP_ENV = production ✅
[ ] APP_KEY set ✅
[ ] Database credentials secure ✅
[ ] Mail credentials set ✅
[ ] reCAPTCHA keys set ✅
[ ] Cloudinary keys set ✅
[ ] Custom domain configured ✅
[ ] SSL enabled (automatic) ✅
```

---

Good luck! Your app will be live in minutes! 🚀
