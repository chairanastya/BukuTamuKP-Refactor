#!/usr/bin/env bash

# 🚀 RAILWAY DEPLOYMENT VERIFICATION SCRIPT
# Check if everything is ready to deploy

echo "════════════════════════════════════════════════════════════"
echo "🔍 RAILWAY DEPLOYMENT READINESS CHECK"
echo "════════════════════════════════════════════════════════════"
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

passed=0
failed=0

check_file() {
    local file=$1
    local description=$2
    
    if [ -f "$file" ] || [ -d "$file" ]; then
        echo -e "${GREEN}✓${NC} $description - FOUND"
        ((passed++))
    else
        echo -e "${RED}✗${NC} $description - MISSING"
        ((failed++))
    fi
}

check_git() {
    echo ""
    echo "📦 GIT REPOSITORY"
    echo "─────────────────────────────────────────────────────────"
    
    if git status > /dev/null 2>&1; then
        echo -e "${GREEN}✓${NC} Git repository initialized"
        ((passed++))
        
        if [ -z "$(git status --porcelain)" ]; then
            echo -e "${GREEN}✓${NC} All changes committed (clean working tree)"
            ((passed++))
        else
            echo -e "${YELLOW}⚠${NC} Uncommitted changes detected"
            echo "  Run: git add . && git commit -m 'Ready for deploy'"
            ((failed++))
        fi
        
        BRANCH=$(git rev-parse --abbrev-ref HEAD)
        echo "  Current branch: $BRANCH"
    else
        echo -e "${RED}✗${NC} Not a git repository"
        ((failed++))
    fi
}

check_dependencies() {
    echo ""
    echo "📚 PROJECT DEPENDENCIES"
    echo "─────────────────────────────────────────────────────────"
    
    check_file "composer.json" "composer.json"
    check_file "composer.lock" "composer.lock"
    check_file "package.json" "package.json"
    check_file "package-lock.json" "package-lock.json"
    
    if [ -f "vendor/autoload.php" ]; then
        echo -e "${GREEN}✓${NC} PHP dependencies installed"
        ((passed++))
    else
        echo -e "${YELLOW}⚠${NC} PHP dependencies not installed"
        echo "  Run: composer install"
    fi
    
    if [ -d "node_modules" ]; then
        echo -e "${GREEN}✓${NC} Node dependencies installed"
        ((passed++))
    else
        echo -e "${YELLOW}⚠${NC} Node dependencies not installed"
        echo "  Run: npm install"
    fi
    
    if [ -d "public/build" ]; then
        echo -e "${GREEN}✓${NC} Frontend assets built"
        ((passed++))
    else
        echo -e "${RED}✗${NC} Frontend assets not built"
        echo "  Run: npm run build"
        ((failed++))
    fi
}

check_deployment_files() {
    echo ""
    echo "🚀 DEPLOYMENT FILES"
    echo "─────────────────────────────────────────────────────────"
    
    check_file "Dockerfile" "Dockerfile (Docker configuration)"
    check_file "Procfile" "Procfile (Heroku/Railway config)"
    check_file ".railwayignore" ".railwayignore (Railway ignore patterns)"
    check_file "railway-deploy.sh" "railway-deploy.sh (Deploy script)"
    check_file ".env.example" ".env.example (Environment template)"
}

check_code_files() {
    echo ""
    echo "📂 CODE STRUCTURE"
    echo "─────────────────────────────────────────────────────────"
    
    check_file "app" "app/ (Application directory)"
    check_file "routes" "routes/ (Routes directory)"
    check_file "resources" "resources/ (Views/Assets directory)"
    check_file "database" "database/ (Migrations/Seeds directory)"
    check_file "bootstrap" "bootstrap/ (Bootstrap cache)"
    check_file "config" "config/ (Configuration files)"
}

check_controllers() {
    echo ""
    echo "🎮 CONTROLLERS"
    echo "─────────────────────────────────────────────────────────"
    
    check_file "app/Http/Controllers/HealthCheckController.php" "HealthCheckController (Health monitoring)"
    check_file "app/Http/Controllers/ResepsionisController.php" "ResepsionisController"
    check_file "app/Http/Controllers/TamuController.php" "TamuController"
}

check_routes() {
    echo ""
    echo "🛣️  ROUTES"
    echo "─────────────────────────────────────────────────────────"
    
    if grep -q "HealthCheckController" routes/web.php 2>/dev/null; then
        echo -e "${GREEN}✓${NC} Health check routes registered"
        ((passed++))
    else
        echo -e "${RED}✗${NC} Health check routes NOT registered"
        ((failed++))
    fi
    
    if grep -q "/health" routes/web.php 2>/dev/null; then
        echo -e "${GREEN}✓${NC} Health endpoints configured"
        ((passed++))
    else
        echo -e "${RED}✗${NC} Health endpoints NOT configured"
        ((failed++))
    fi
}

check_configuration() {
    echo ""
    echo "⚙️  CONFIGURATION"
    echo "─────────────────────────────────────────────────────────"
    
    if [ -f ".env" ]; then
        echo -e "${GREEN}✓${NC} .env file exists (LOCAL ONLY)"
        ((passed++))
    else
        echo -e "${YELLOW}⚠${NC} .env file not found (needed locally)"
    fi
    
    if grep -q "APP_KEY=" .env.example 2>/dev/null; then
        echo -e "${GREEN}✓${NC} APP_KEY in .env.example"
        ((passed++))
    else
        echo -e "${RED}✗${NC} APP_KEY missing from .env.example"
        ((failed++))
    fi
    
    if grep -q "DB_CONNECTION=pgsql" .env.example 2>/dev/null; then
        echo -e "${GREEN}✓${NC} PostgreSQL configured"
        ((passed++))
    else
        echo -e "${YELLOW}⚠${NC} PostgreSQL not configured in example"
    fi
}

check_database() {
    echo ""
    echo "🗄️  DATABASE"
    echo "─────────────────────────────────────────────────────────"
    
    check_file "database/migrations" "database/migrations/ (Migrations)"
    
    migration_count=$(find database/migrations -name "*.php" 2>/dev/null | wc -l)
    if [ $migration_count -gt 0 ]; then
        echo -e "${GREEN}✓${NC} Found $migration_count migration files"
        ((passed++))
    else
        echo -e "${RED}✗${NC} No migration files found"
        ((failed++))
    fi
    
    check_file "database/seeders" "database/seeders/ (Seeders)"
}

check_documentation() {
    echo ""
    echo "📖 DOCUMENTATION"
    echo "─────────────────────────────────────────────────────────"
    
    check_file "RAILWAY_README.md" "RAILWAY_README.md"
    check_file "RAILWAY_QUICK_START.md" "RAILWAY_QUICK_START.md"
    check_file "RAILWAY_DEPLOYMENT.md" "RAILWAY_DEPLOYMENT.md"
    check_file "RAILWAY_DEPLOYMENT_CHECKLIST.md" "RAILWAY_DEPLOYMENT_CHECKLIST.md"
}

# Run all checks
check_git
check_dependencies
check_deployment_files
check_code_files
check_controllers
check_routes
check_configuration
check_database
check_documentation

# Summary
echo ""
echo "════════════════════════════════════════════════════════════"
echo "📊 SUMMARY"
echo "════════════════════════════════════════════════════════════"

total=$((passed + failed))

echo -e "Checks passed: ${GREEN}${passed}${NC}"
echo -e "Checks failed: ${RED}${failed}${NC}"
echo -e "Total: $total"
echo ""

if [ $failed -eq 0 ]; then
    echo -e "${GREEN}✅ ALL CHECKS PASSED - READY TO DEPLOY!${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Push to GitHub: git push origin \$(git rev-parse --abbrev-ref HEAD)"
    echo "2. Go to https://railway.app"
    echo "3. Create new project from GitHub"
    echo "4. Add environment variables from RAILWAY_README.md"
    echo "5. Deploy!"
    exit 0
else
    echo -e "${RED}❌ SOME CHECKS FAILED - FIX BEFORE DEPLOYING${NC}"
    exit 1
fi
