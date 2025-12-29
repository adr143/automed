const mysql = require('mysql2/promise');
const fs = require('fs');

async function importDatabase() {
  const connection = await mysql.createConnection({
    host: process.env.RAILWAY_TCP_PROXY_DOMAIN || process.env.MYSQLHOST,
    port: process.env.RAILWAY_TCP_PROXY_PORT || 3306,
    user: process.env.MYSQLUSER,
    password: process.env.MYSQLPASSWORD,
    database: process.env.MYSQLDATABASE,
  });

  const sql = fs.readFileSync('database/medicine_dispenser.sql', 'utf8');
  const statements = sql.split(';').filter(stmt => stmt.trim());

  for (const statement of statements) {
    if (statement.trim()) {
      try {
        await connection.query(statement);
      } catch (err) {
        if (!statement.trim().toUpperCase().startsWith('SET')) {
          console.error('Error executing:', statement.substring(0, 50), err.message);
        }
      }
    }
  }

  await connection.end();
  console.log('Database imported successfully!');
}

importDatabase().catch(console.error);