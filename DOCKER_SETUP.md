# 🐳 Docker Setup Guide - MediDesk

Complete guide to running MediDesk with Docker.

---

## 📋 Prerequisites

- Docker & Docker Compose installed
- No services running on ports: 8080, 5173, 3317, 8081

---

## 🚀 Quick Start

### 1. Copy Environment File
```bash
cp .env.docker .env
```

### 2. Build & Start Containers
```bash
docker-compose up -d --build
```

This will:
- Build PHP image with all extensions
- Start MySQL database
- Start Nginx web server
- Start Node dev server
- Run migrations automatically
- Generate APP_KEY

### 3. Wait for Services
```bash
# Watch container logs
docker-compose logs -f

# Wait for "✨ MediDesk is ready!" message
```

### 4. Access Application
```
🌐 Application:      http://localhost:8080
⚡ Vite Dev Server:  http://localhost:5173
📊 PhpMyAdmin:       http://localhost:8081 (root/root)
```

---

## 🎮 Common Commands

### Start Containers
```bash
docker-compose up -d
```

### Stop Containers
```bash
docker-compose down
```

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f php
docker-compose logs -f nginx
docker-compose logs -f mysql
docker-compose logs -f node
```

### Access Container Shell
```bash
# PHP container
docker-compose exec php bash

# MySQL
docker-compose exec mysql mysql -u root -proot

# Node
docker-compose exec node bash
```

---

## 🛠️ Laravel Artisan Commands

```bash
# Migrate database
docker-compose exec php php artisan migrate

# Seed database
docker-compose exec php php artisan db:seed

# Cache clear
docker-compose exec php php artisan cache:clear

# Generate APP_KEY
docker-compose exec php php artisan key:generate

# Run tests
docker-compose exec php php artisan test

# Create new migration
docker-compose exec php php artisan make:migration create_table_name

# Tinker (REPL)
docker-compose exec php php artisan tinker
```

---

## 📦 Node/NPM Commands

```bash
# Install dependencies
docker-compose exec node npm install

# Build for production
docker-compose exec node npm run build

# Run dev server
docker-compose exec node npm run dev
```

---

## 🗄️ Database Management

### Access MySQL via CLI
```bash
docker-compose exec mysql mysql -u root -proot medidesk
```

### PhpMyAdmin
Navigate to: **http://localhost:8081**
- Username: `root`
- Password: `root`
- Database: `medidesk`

### Database Port
MySQL is accessible from host machine on: **localhost:3317**

```bash
# Connect from local MySQL client
mysql -h localhost -P 3317 -u root -proot medidesk
```

---

## 🔧 Troubleshooting

### Container Won't Start
```bash
# Check for port conflicts
docker ps
docker-compose logs -f
```

### MySQL Connection Error
Wait 30 seconds for MySQL to be ready. The health check ensures it's healthy before PHP starts.

```bash
# Manually check MySQL
docker-compose exec php mysql -hmysql -uroot -proot -e "SELECT 1"
```

### Vite Dev Server Not Connecting
```bash
# Restart node container
docker-compose restart node

# Check logs
docker-compose logs -f node
```

### Build Cache Issues
```bash
# Rebuild without cache
docker-compose build --no-cache

# Restart
docker-compose up -d
```

### Clear All Containers & Volumes
```bash
# ⚠️ WARNING: Deletes database!
docker-compose down -v
docker-compose up -d --build
```

---

## 📊 Service Health Status

Check if all services are healthy:
```bash
docker-compose ps
```

Expected output:
```
NAME               STATUS
medidesk_php       Up ... (healthy)
medidesk_nginx     Up ... (healthy)
medidesk_mysql     Up ... (healthy)
medidesk_node      Up ...
medidesk_phpmyadmin Up ...
```

---

## 📝 File Structure

```
medidesk/
├── Dockerfile              # PHP 8.2 FPM container
├── docker-compose.yml      # Multi-container orchestration
├── docker/
│   ├── entrypoint.sh       # Auto-initialization script
│   ├── nginx.conf          # Nginx configuration
│   └── php.ini             # PHP configuration (optional)
├── .dockerignore           # Build context exclusions
├── .env.docker             # Docker environment variables
└── DOCKER_SETUP.md         # This file
```

---

## 🌍 Environment Variables

Edit `.env` or `.env.docker`:

```env
# App Configuration
APP_NAME=MediDesk
APP_ENV=local
APP_URL=http://localhost:8080

# Database (Docker service names)
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=medidesk
DB_USERNAME=root
DB_PASSWORD=root

# Vite Dev Server
VITE_PORT=5173
VITE_URL=http://localhost:5173
```

---

## 🐛 Debug Mode

Enable debug logging:

```bash
# Update .env
APP_DEBUG=true
LOG_LEVEL=debug

# Rebuild containers
docker-compose up -d --build
```

View PHP logs:
```bash
docker-compose logs -f php
```

---

## 🚢 Production Notes

For production deployment:
- Use `docker-compose.prod.yml` (not included)
- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Use environment secrets, not `.env`
- Add SSL certificate
- Restrict database access
- Use strong passwords

---

## 📚 References

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Laravel Docker](https://laravel.com/docs/docker)
- [nginx](https://nginx.org/)
- [MySQL](https://www.mysql.com/)

---

**Need help?** Check Docker logs: `docker-compose logs -f`
