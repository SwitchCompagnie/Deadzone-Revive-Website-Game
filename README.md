# Deadzone Revive Website Game

Laravel-based web platform for the Deadzone project.

## How to Set Up (Local Development)

1. Install [XAMPP](https://www.apachefriends.org/index.html) or any local PHP server stack (Apache, PHP, MySQL/MariaDB).

2. Install [Composer](https://getcomposer.org/) — the PHP dependency manager.

3. Clone this repository into your web server root.  
   Example for XAMPP on Windows:  
   `C:\xampp\htdocs\deadzone-revive-website`

4. Start Apache and MySQL/MariaDB from the XAMPP Control Panel.

5. Open a terminal in the project directory and install dependencies:

   ```bash
   composer install
   ```

6. Create a copy of the `.env` file:

   ```bash
   cp .env.example .env
   ```

   On Windows:

   ```bat
   copy .env.example .env
   ```

7. Set up your `.env` with the correct database credentials.  
   Example config for local MariaDB:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=deadzone_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

8. Generate the Laravel application key:

   ```bash
   php artisan key:generate
   ```

9. Create the database in phpMyAdmin or any MySQL client (e.g., `deadzone_db`).

10. Run database migrations:

    ```bash
    php artisan migrate
    ```

    If you have seeders:

    ```bash
    php artisan db:seed
    ```

11. Configure Apache to serve from Laravel’s `public/` folder:

    - Edit your Apache config or VirtualHost (e.g., in `httpd-vhosts.conf`)
    - Example:

      ```apache
      <VirtualHost *:80>
          DocumentRoot "C:/xampp/htdocs/deadzone-revive-website/public"
          ServerName localhost

          <Directory "C:/xampp/htdocs/deadzone-revive-website/public">
              AllowOverride All
              Require all granted
          </Directory>
      </VirtualHost>
      ```

    - Don’t forget to restart Apache after changes.

12. Visit the app in your browser:

    ```
    http://localhost
    ```

👉 Join our [Discord](https://discord.gg/jFyAePxDBJ) for support or questions.

---

## Development

### Requirements

- PHP
- Composer
- MariaDB (MySQL also supported)
- Apache (or Nginx)

### File Structure

Key Laravel project structure:

```
app/           # Application logic
config/        # Configuration files
database/      # Migrations and seeders
public/        # Web root (served by Apache)
resources/     # Views, assets
routes/        # Route definitions
.env           # Environment config
```

### Social Login Setup

You can configure OAuth login for Discord, Twitter, and GitHub in your `.env` file:

```env
# Discord OAuth
DISCORD_CLIENT_ID=your_discord_id
DISCORD_CLIENT_SECRET=your_discord_secret
DISCORD_REDIRECT=http://localhost/auth/discord/callback

# Twitter OAuth
TWITTER_CLIENT_ID=your_twitter_id
TWITTER_CLIENT_SECRET=your_twitter_secret
TWITTER_REDIRECT=http://localhost/auth/twitter/callback

# GitHub OAuth
GITHUB_CLIENT_ID=your_github_id
GITHUB_CLIENT_SECRET=your_github_secret
GITHUB_REDIRECT=http://localhost/auth/github/callback
```

Make sure these routes exist in your app and are correctly handled in your auth controller or service.

---

## Useful Commands

```bash
# Clear config/cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Re-run migrations
php artisan migrate:fresh --seed
```

---

## Common Issues

- **Blank page or 500 error**  
  → Check `storage/logs/` for Laravel error logs.  
  → Ensure `storage/` and `bootstrap/cache/` folders are writable.

- **MySQL connection error**  
  → Verify `.env` DB credentials match your local setup.  
  → Ensure MySQL is running.

- **"Could not find driver"**  
  → Enable `pdo_mysql` in your `php.ini`.

- **Routes not working**  
  → Ensure `mod_rewrite` is enabled in Apache.  
  → `.htaccess` in the `public/` folder must be active.

---

## Summary Quick Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Then access it via:  
👉 `http://localhost` (Apache must point to `/public`)

---
