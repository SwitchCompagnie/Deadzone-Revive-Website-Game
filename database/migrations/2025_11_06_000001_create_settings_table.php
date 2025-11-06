<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('key');
        });

        // Insert default maintenance mode settings
        DB::table('settings')->insert([
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable or disable maintenance mode',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'The Last Stand: Dead Zone is down for scheduled maintenance. We apologize for any inconvenience.',
                'type' => 'string',
                'description' => 'Message to display during maintenance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_eta',
                'value' => '00:00',
                'type' => 'string',
                'description' => 'Estimated time of completion (local time)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
