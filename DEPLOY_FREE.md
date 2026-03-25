# Free Deployment Guide (Frontend + Backend)

This guide uses only free tiers:
- Frontend: Netlify (static hosting)
- Backend: Render (Docker web service)
- MySQL: TiDB Cloud Serverless (free) or any free MySQL provider
- Redis: Upstash (free)
- MongoDB: MongoDB Atlas M0 (free)

## 1. Prepare Cloud Databases (Free)

Create these resources and collect connection values:

1. MySQL (TiDB Serverless or equivalent)
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLDATABASE`
- `MYSQLUSER`
- `MYSQLPASSWORD`

2. Redis (Upstash)
- `REDIS_URL` (use `rediss://...` URL)

3. MongoDB (Atlas)
- `MONGODB_URI`
- `MONGODB_DATABASE` (example: `internship_app`)

## 2. Deploy Backend to Render (Free)

1. Push repository to GitHub.
2. In Render, click `New +` -> `Blueprint`.
3. Select your repository (it will read `render.yaml`).
4. Open service `internship-app-backend` and add environment variables:
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLDATABASE`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `REDIS_URL`
- `MONGODB_URI`
- `MONGODB_DATABASE`
- `ALLOWED_ORIGINS` (set later after frontend URL is known)
5. Deploy service.
6. Confirm health URL works:
- `https://<your-render-service>.onrender.com/`

Expected response:
```json
{"status":"ok","message":"Backend is running"}
```

## 3. Deploy Frontend to Netlify (Free)

1. In Netlify, click `Add new site` -> `Import an existing project`.
2. Connect GitHub repo.
3. Set base directory to `frontend`.
4. Build command: leave empty.
5. Publish directory: `.`
6. Deploy site.

Your frontend URL will look like:
- `https://<your-site>.netlify.app`

## 4. Connect Frontend to Backend

Update `frontend/js/config.js` to your Render backend URL:

```js
window.APP_CONFIG = window.APP_CONFIG || {};
window.APP_CONFIG.API_BASE = "https://<your-render-service>.onrender.com";
```

Commit and push. Netlify auto-redeploys.

## 5. Update Backend CORS Allowlist

In Render backend environment variables, set:

```env
ALLOWED_ORIGINS=https://<your-site>.netlify.app,http://localhost:5500,http://127.0.0.1:5500
```

Redeploy backend.

## 6. Verify End-to-End

1. Open `https://<your-site>.netlify.app`
2. Register user.
3. Login.
4. Save profile.
5. Logout/login to validate Redis session + profile persistence.

## Notes

- Render free services may sleep after inactivity; first request can be slow.
- Never commit `backend/.env` with secrets.
- If CORS errors appear, confirm `ALLOWED_ORIGINS` exactly matches your frontend URL (including protocol).
