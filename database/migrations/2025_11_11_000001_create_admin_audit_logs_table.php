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
        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable(); // Stocké au cas où l'utilisateur est supprimé
            $table->string('action'); // view, create, update, delete
            $table->string('resource_type'); // Ex: App\Models\User, App\Models\PlayerAccount
            $table->string('resource_name')->nullable(); // Nom lisible de la ressource
            $table->string('resource_id')->nullable(); // ID de l'enregistrement affecté
            $table->string('resource_title')->nullable(); // Titre/nom de l'enregistrement pour affichage
            $table->text('description')->nullable(); // Description de l'action
            $table->json('old_values')->nullable(); // Anciennes valeurs (pour update/delete)
            $table->json('new_values')->nullable(); // Nouvelles valeurs (pour create/update)
            $table->json('metadata')->nullable(); // Métadonnées supplémentaires
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index('user_id');
            $table->index('resource_type');
            $table->index('action');
            $table->index('created_at');
            $table->index(['resource_type', 'resource_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_audit_logs');
    }
};
