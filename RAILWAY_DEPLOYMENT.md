# Railway Deployment Guide

## Prerequisites
- Railway account
- MySQL service added to your Railway project

## Deployment Steps

1. **Connect your repository to Railway**
   - Fork or clone this repository
   - Connect it to your Railway project

2. **Add MySQL Service**
   - In your Railway project, add a MySQL service
   - Railway will automatically generate the required environment variables

3. **Environment Variables**
   The following variables are automatically set by Railway MySQL service:
   - `MYSQL_DATABASE`
   - `MYSQL_ROOT_PASSWORD`
   - `MYSQLDATABASE`
   - `MYSQLHOST`
   - `MYSQLPASSWORD`
   - `MYSQLPORT`
   - `MYSQLUSER`

4. **Deploy**
   - Push your code to the connected repository
   - Railway will automatically build and deploy
   - The `install.php` script will run automatically to set up the database

## Default Admin Account
- Email: admin@admin.com
- Password: admin123

## Files Added for Railway Deployment
- `install.php` - Automatic database setup script
- `railway.json` - Railway deployment configuration
- `nixpacks.toml` - Build configuration
- `.env` - Environment variables template

## Troubleshooting
- Check Railway logs if deployment fails
- Ensure MySQL service is running
- Verify environment variables are set correctly