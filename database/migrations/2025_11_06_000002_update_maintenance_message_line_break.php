<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update the maintenance message to ensure proper line break
        DB::table('settings')
            ->where('key', 'maintenance_message')
            ->update([
                'value' => "The Last Stand: Dead Zone is down for scheduled maintenance.\nWe apologize for any inconvenience.",
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Restore original message (optional)
        DB::table('settings')
            ->where('key', 'maintenance_message')
            ->update([
                'value' => "The Last Stand: Dead Zone is down for scheduled maintenance. We apologize for any inconvenience.",
                'updated_at' => now(),
            ]);
    }
};
