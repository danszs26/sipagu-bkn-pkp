<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');            // created | updated | deleted | exported | login | dst
            $table->string('model_type');        // ex: Transaction, BudgetCategory
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('description');       // ringkasan human-readable
            $table->json('old_data')->nullable(); // snapshot data SEBELUM perubahan
            $table->json('new_data')->nullable(); // snapshot data SESUDAH perubahan
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['model_type', 'model_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
