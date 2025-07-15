# Deployment Guide for Secure Notes Manager

This application is a Laravel backend with a React frontend that can be deployed to Render.

## Prerequisites

- A Render account
- A PostgreSQL database (Render provides this)

## Deployment Steps

1. **Connect your repository to Render**
   - Go to your Render dashboard
   - Click "New +" and select "Web Service"
   - Connect your GitHub repository

2. **Configure the service**
   - **Name**: secure-notes-manager (or your preferred name)
   - **Environment**: PHP
   - **Build Command**: `composer install --no-dev --optimize-autoloader && cd frontend && npm install && npm run build && cd .. && cp -r frontend/build/* public/`
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

3. **Set Environment Variables**
   - `APP_ENV`: production
   - `APP_DEBUG`: false
   - `APP_URL`: https://your-app-name.onrender.com
   - `APP_KEY`: (will be generated automatically)
   - `DB_CONNECTION`: pgsql
   - `DB_HOST`: (from your PostgreSQL service)
   - `DB_PORT`: 5432
   - `DB_DATABASE`: (from your PostgreSQL service)
   - `DB_USERNAME`: (from your PostgreSQL service)
   - `DB_PASSWORD`: (from your PostgreSQL service)
   - `CACHE_DRIVER`: file
   - `SESSION_DRIVER`: file
   - `QUEUE_CONNECTION`: sync

4. **Create a PostgreSQL Database**
   - In Render, create a new PostgreSQL service
   - Copy the connection details to your environment variables

5. **Deploy**
   - Click "Create Web Service"
   - Render will automatically build and deploy your application

## How it Works

1. **Build Process**:
   - Installs PHP dependencies via Composer
   - Installs Node.js dependencies for the React frontend
   - Builds the React app
   - Copies the built React app to Laravel's public directory

2. **Runtime**:
   - Laravel serves the React app for all non-API routes
   - API routes are handled by Laravel controllers
   - The React app communicates with the API using relative URLs

## Troubleshooting

- **Build fails**: Check that all dependencies are properly specified in composer.json and package.json
- **Database connection fails**: Verify your PostgreSQL environment variables
- **React app doesn't load**: Check that the build process completed successfully and files were copied to public/

## Local Development

For local development, you can run:
- Backend: `php artisan serve --port=8001`
- Frontend: `cd frontend && npm start`

The frontend will proxy API requests to the Laravel backend running on port 8001. 