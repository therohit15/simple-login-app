# Internship App - Windows Run Guide

This project has:
- `backend/` -> PHP API (MySQL + Redis + MongoDB)
- `frontend/` -> static HTML/CSS/JS app

## Prerequisites (Windows)

Install:
- PHP 8.2+ (with extensions: `pdo_mysql`, `mongodb`, `redis`)
- Composer
- Python 3

Verify tools:

```powershell
php -v
composer -V
python --version
```

## 1. Configure Environment

Update `backend/.env` with your credentials:

```env
MYSQLHOST=...
MYSQLPORT=...
MYSQLDATABASE=...
MYSQLUSER=...
MYSQLPASSWORD=...

REDIS_URL=rediss://default:...@...:6379

MONGODB_URI=mongodb+srv://...:...@.../?appName=...
MONGODB_DATABASE=internship_app
```

## 2. Install Backend Dependencies

Open PowerShell (or CMD) in project root:

```powershell
cd "C:\path\to\Internship-app-main\backend"
composer install
```

## 3. Run Backend (Port 8000)

From `backend/`:

```powershell
php -S 127.0.0.1:8000 index.php
```

Backend URL:
- `http://127.0.0.1:8000`

## 4. Run Frontend (Port 5500)

Open a second terminal and run:

```powershell
cd "C:\path\to\Internship-app-main\frontend"
python -m http.server 5500 --bind 127.0.0.1
```

Frontend URL:
- `http://127.0.0.1:5500/pages/signUp.html`

## 5. Test Flow

Use this sequence:
1. Register
2. Login
3. Redirect to Profile
4. Save profile details

## Troubleshooting

### Address already in use (8000 or 5500)

Find process using port:

```powershell
netstat -ano | findstr :8000
netstat -ano | findstr :5500
```

Kill process by PID:

```powershell
taskkill /PID <PID> /F
```

### Backend not starting

- Confirm you are running command inside `backend/`.
- Confirm `.env` exists and values are valid.
- Confirm required PHP extensions are enabled:

```powershell
php -m
```

### Frontend works but APIs fail

- Ensure backend is running on `127.0.0.1:8000`.
- Ensure frontend JS points to `http://127.0.0.1:8000` as API base.

