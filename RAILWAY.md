# Railway Deployment Guide

This Laravel application is configured for deployment on Railway using Docker.

## Quick Deploy

1. Push your code to GitHub
2. Create a new project on Railway
3. Connect your GitHub repository
4. Railway will automatically detect the `Dockerfile` and build

## Required Environment Variables

Set these in your Railway project settings:

```
APP_NAME=YourAppName
APP_ENV=production
APP_KEY=base64:your-key-here
APP_DEBUG=false
APP_URL=https://your-app.railway.app

DB_CONNECTION=sqlite

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

LOG_CHANNEL=stack
LOG_LEVEL=error
```

## Generate APP_KEY

Run locally to generate a key:
```bash
php artisan key:generate --show
```

Then copy the output to Railway's `APP_KEY` variable.

## What's Included

- **Nginx**: Web server on port 8080
- **PHP-FPM**: PHP processor
- **Queue Worker**: Background job processing
- **Supervisor**: Process management
- **SQLite**: Database (persistent via Railway volumes)

## Database Persistence

Railway automatically provides persistent storage. The SQLite database at `database/database.sqlite` will persist across deployments.

## Logs

View logs in Railway dashboard or via CLI:
```bash
railway logs
```

## Local Testing

Build and test the Docker image locally:
```bash
docker build -t laravel-app .
docker run -p 8080:8080 --env-file .env laravel-app
```

Visit http://localhost:8080

## Troubleshooting

- **500 Error**: Check `APP_KEY` is set correctly
- **Database Issues**: Ensure migrations ran (check logs)
- **Asset Issues**: Verify `npm run build` completed during build
- **Permission Issues**: Check storage and cache directories are writable
