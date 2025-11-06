# Maintenance Mode Broadcast Setup

## Overview
When maintenance mode is enabled, the system will automatically broadcast maintenance messages to all connected players every 30 seconds.

## Features
- **Automatic Broadcasting**: Messages are sent every 30 seconds when maintenance mode is active
- **Warning Protocol**: Messages use the 'warn' protocol for visibility
- **ETA Information**: Includes the estimated completion time in each broadcast
- **No Overlap**: Prevents multiple broadcasts from running simultaneously

## Requirements

### Laravel Scheduler
The maintenance broadcast feature requires the Laravel scheduler to be running. Ensure you have added the following cron entry:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or use the recommended approach for Laravel 11:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:work
```

### Manual Testing
To manually trigger a maintenance broadcast:

```bash
php artisan maintenance:broadcast
```

This command will:
1. Check if maintenance mode is active
2. If active, send a broadcast with the configured message and ETA
3. Log the result to the Laravel log

## How It Works

1. **Command**: `BroadcastMaintenanceMessage` (app/Console/Commands/BroadcastMaintenanceMessage.php)
   - Checks if maintenance mode is enabled
   - Retrieves maintenance message and ETA from settings
   - Broadcasts to all connected players via game server API
   - Logs success/failure

2. **Schedule**: Configured in `routes/console.php`
   - Runs every 30 seconds
   - Uses `withoutOverlapping()` to prevent concurrent executions
   - Runs in background to not block other scheduled tasks

3. **Admin Panel**: Maintenance Mode page (/admin/maintenance-mode)
   - Shows active status with broadcast notification
   - Configure message and ETA
   - Changes take effect on next broadcast cycle

## Troubleshooting

### Broadcasts not working?
1. Verify scheduler is running: `ps aux | grep schedule`
2. Check Laravel logs: `tail -f storage/logs/laravel.log`
3. Test manually: `php artisan maintenance:broadcast`
4. Verify game server API is accessible: `curl <API_BASE_URL>/api/broadcast/send`

### Stop broadcasts
Simply disable maintenance mode in the admin panel. The command will check the status and skip broadcasting if disabled.
