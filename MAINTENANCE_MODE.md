# Maintenance Mode System

## Overview

This system provides a professional and database-driven maintenance mode for the Deadzone Revive application. When enabled, it prevents users from logging in and accessing the game while allowing administrators to continue using the site.

## Features

- **Database-driven**: All settings are stored in the database and can be changed without code deployment
- **Admin bypass**: Administrators can still access the site during maintenance
- **Customizable message**: Configure the maintenance message displayed to users
- **ETA display**: Show estimated completion time to users
- **Real-time checks**: Frontend checks maintenance status via API
- **Login blocking**: Automatically disables login buttons and social authentication
- **Game access blocking**: Prevents access to the game view during maintenance

## Database Structure

The system uses a `settings` table with the following key-value pairs:

| Key | Type | Description |
|-----|------|-------------|
| `maintenance_mode` | boolean | Enable/disable maintenance mode |
| `maintenance_message` | string | Message displayed to users |
| `maintenance_eta` | string | Estimated completion time (HH:MM format) |

## How to Use

### Via Filament Admin Panel

1. Login to the admin panel at `/admin`
2. Navigate to **Maintenance Mode** in the sidebar
3. Toggle the maintenance mode switch
4. Customize the message and ETA
5. Click **Save Settings**
6. Confirm the changes

### Via Code (Programmatically)

```php
use App\Models\Setting;

// Enable maintenance mode
Setting::set('maintenance_mode', 'true', 'boolean');
Setting::set('maintenance_message', 'Down for scheduled maintenance.');
Setting::set('maintenance_eta', '14:30');

// Disable maintenance mode
Setting::set('maintenance_mode', 'false', 'boolean');

// Check if maintenance mode is enabled
$isMaintenanceMode = Setting::isMaintenanceMode();

// Get maintenance details
$message = Setting::getMaintenanceMessage();
$eta = Setting::getMaintenanceETA();
```

### Via Artisan (if needed)

```bash
# Run the migration to create the settings table
php artisan migrate

# Clear cache to ensure immediate updates
php artisan cache:clear
```

## Frontend Integration

### Login Page
- Automatically checks maintenance status on page load
- Disables login button and social authentication buttons
- Shows maintenance message with ETA
- Prevents form submission during maintenance

### Game Page
- Checks maintenance status before loading the game
- Displays maintenance screen if enabled
- Prevents game initialization

### API Endpoint

**GET** `/api/maintenance/status`

Returns:
```json
{
  "maintenance": true,
  "message": "The Last Stand: Dead Zone is down for scheduled maintenance.",
  "eta": "14:30"
}
```

## Middleware

The `CheckMaintenanceMode` middleware is applied to:
- All guest routes (login, social auth, password reset)
- The game route

**Admin bypass**: Users with `is_admin = true` can bypass maintenance mode.

## Files Modified/Created

### New Files
- `database/migrations/2025_11_06_000001_create_settings_table.php`
- `app/Models/Setting.php`
- `app/Http/Middleware/CheckMaintenanceMode.php`
- `app/Http/Controllers/MaintenanceController.php`
- `app/Filament/Pages/MaintenanceMode.php`
- `resources/views/maintenance.blade.php`
- `resources/views/filament/pages/maintenance-mode.blade.php`

### Modified Files
- `routes/web.php` - Added API endpoint and middleware
- `public/assets/js/game.js` - Added API-based maintenance check
- `public/assets/js/login.js` - Added login blocking during maintenance

## Maintenance Page

A beautiful maintenance page is displayed when users try to access the site during maintenance. It includes:
- Site logo
- Maintenance icon with pulse animation
- Custom message
- ETA display
- Links to social media for updates

Preview the maintenance page at: `/maintenance/preview`

## Caching

Settings are cached for 1 hour to improve performance. The cache is automatically cleared when settings are updated via the admin panel.

To manually clear the cache:
```php
Setting::clearCache();
```

Or via Artisan:
```bash
php artisan cache:clear
```

## Testing

1. Enable maintenance mode via admin panel
2. Open an incognito window and try to access the site
3. Verify that the maintenance page is displayed
4. Verify that login button is disabled
5. Login as admin and verify you can still access the site
6. Disable maintenance mode
7. Verify that normal users can access the site again

## Security Considerations

- Only administrators can change maintenance mode settings
- The API endpoint is publicly accessible but read-only
- User sessions are preserved during maintenance mode
- Maintenance checks are cached to prevent excessive database queries

## Troubleshooting

### Changes not taking effect
- Clear application cache: `php artisan cache:clear`
- Hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)

### Can't access admin panel during maintenance
- Ensure your user has `is_admin = true` in the database
- Check that middleware is correctly configured in routes

### API endpoint not working
- Verify the route is registered in `routes/web.php`
- Check that the `MaintenanceController` exists
- Ensure the `settings` table exists (run migrations)

## Future Improvements

Potential enhancements for the maintenance mode system:

- Scheduled maintenance (automatic enable/disable at specific times)
- IP whitelist for testing during maintenance
- Maintenance mode logging and history
- Multiple language support for messages
- Email notifications to admins when maintenance is enabled/disabled
- Integration with external status page services
