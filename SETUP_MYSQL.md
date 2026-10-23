# MySQL Database Setup

## Step 1: Start XAMPP
Open XAMPP Control Panel and start **Apache** and **MySQL**.

## Step 2: Create Database
Open **phpMyAdmin** at http://localhost/phpmyadmin and run:

```sql
CREATE DATABASE IF NOT EXISTS `learning_system`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

Or use the "New" button in phpMyAdmin left sidebar and name it `learning_system`.

## Step 3: Run Migrations
Open a terminal/CMD in the project root (`c:\xampp\htdocs\a\lj\learning_system`) and run:

```
php artisan migrate --force
```

This creates all tables and seeds the default accounts.

## Step 4: Clear Cache
```
php artisan config:clear
php artisan cache:clear
```

## Step 5: Create Storage Link
```
php artisan storage:link
```

## Step 6: Access the Site
Open: http://localhost/a/lj/learning_system/public

## Seeded Accounts (created automatically by migrations)

| Role    | Email                          | Password  |
|---------|-------------------------------|-----------|
| Admin   | admin@gmail.com               | 12345678  |
| Staff   | akpatel2029@gmail.com         | 12345678  |
| Finance | finance@learningcurve.com     | 12345678  |

## Environment (already configured in .env)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=learning_system
DB_USERNAME=root
DB_PASSWORD=
```
