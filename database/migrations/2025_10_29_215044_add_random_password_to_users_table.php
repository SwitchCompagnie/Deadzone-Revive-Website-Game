<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('random_password')->nullable()->after('password');

            // Add indexes for better performance on social login lookups
            $table->index('discord_id');
            $table->index('twitter_id');
            $table->index('github_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['discord_id']);
            $table->dropIndex(['twitter_id']);
            $table->dropIndex(['github_id']);
            $table->dropColumn('random_password');
        });
    }
};
