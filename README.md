# 🩺 MediDesk - Clinic Appointment Management System

A modern single-page application for managing clinic appointments, built using **Laravel 11**, **Vue 3**, **Inertia.js**, and **Tailwind CSS**.

## 🚀 Features

- 👤 User Authentication (Login/Register)
- 📝 Add new appointments
- 🔄 Avoid time conflicts with 30-min slot logic
- ✅ Mark appointments as complete
- ❌ Cancel/delete appointments
- 📅 Sort by date & time (ascending)
- 💨 SPA experience with Inertia.js
- 📊 Export appointments to Excel
- 🔍 Search and paginate completed appointments

## 📸 Screenshots

> *(pending)*

## 🛠️ Tech Stack

- **Backend:** Laravel 11
- **Frontend:** Vue 3 + Inertia.js
- **Styling:** Tailwind CSS
- **Database:** MySQL / SQLite
- **Container:** Docker & Docker Compose

## 🧪 Setup Instructions

### Option 1: Docker (Recommended)
```bash
git clone https://github.com/your-username/medidesk.git
cd medidesk

cp .env.docker .env
docker-compose up -d --build

# Wait for services to initialize
docker-compose logs -f
```

For detailed Docker setup, see [DOCKER_SETUP.md](DOCKER_SETUP.md)

**Access:**
- 🌐 App: http://localhost:8080
- ⚡ Vite: http://localhost:5173
- 📊 PhpMyAdmin: http://localhost:8081 (root/root)
