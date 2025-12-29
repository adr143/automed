# Setup Guide for InfinityFree

## Step 1: Get Database Credentials from InfinityFree

1. Login to your InfinityFree control panel
2. Go to **Databases** section
3. Create a new MySQL database
4. Copy these credentials:
   - Database Name
   - Database User
   - Database Password

## Step 2: Create .env File

1. Copy `.env.example` to `.env`
2. Update with your InfinityFree credentials:

```env
DB_HOST=localhost
DB_NAME=your_infinityfree_db_name
DB_USER=your_infinityfree_db_user
DB_PASS=your_infinityfree_db_password
```

## Step 3: Import Database Schema

1. Go to InfinityFree control panel → **Databases**
2. Click **Manage** on your database
3. Click **phpMyAdmin**
4. Click **Import** tab
5. Upload or paste the contents of `database/medicine_dispenser.sql`
6. Click **Go/Execute**

## Step 4: Upload Files

Upload all files to your InfinityFree hosting via FTP:
- Include the `.env` file (but add to `.gitignore`)
- Do NOT include `node_modules` folder
- Do NOT commit `.env` to Git

## Step 5: Test Connection

Visit your InfinityFree URL and you should see the login page without database errors.

## Troubleshooting

**Error: "Connection error: No such file or directory"**
- Ensure `.env` file exists in the root directory
- Verify DB_HOST is `localhost` (InfinityFree requirement)
- Check DB_NAME, DB_USER, DB_PASS are correct in .env

**Error: "Access denied for user"**
- Double-check database username and password
- Make sure the user exists in InfinityFree's database panel

**Database not found**
- Import the SQL schema via phpMyAdmin
- Verify database name matches in .env file
