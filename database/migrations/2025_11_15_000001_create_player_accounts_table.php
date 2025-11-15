<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_accounts', function (Blueprint $table) {
            $table->string('player_id', 36)->primary();
            $table->text('hashed_password');
            $table->string('email', 255);
            $table->string('display_name', 100);
            $table->string('avatar_url', 500);
            $table->bigInteger('created_at');
            $table->bigInteger('last_login');
            $table->string('country_code', 10)->nullable();
            $table->text('server_metadata_json');
            $table->text('player_objects_json');
            $table->text('neighbor_history_json');
            $table->text('inventory_json');
            $table->text('pay_vault_json');

            // Add indexes for better performance
            $table->index('email');
            $table->index('display_name');
            $table->index('last_login');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_accounts');
    }
};
