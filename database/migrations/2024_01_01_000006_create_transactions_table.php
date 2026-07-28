<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('no_referensi')->unique();
            $table->date('tanggal');
            $table->foreignId('budget_category_id')->constrained('budget_categories');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors');
            $table->decimal('nominal', 18, 2);
            $table->text('uraian');
            $table->string('bukti_file_path');
            $table->string('bukti_file_original_name')->nullable();

            // Anti double-input
            $table->uuid('idempotency_token')->unique();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tanggal']);
            $table->index(['budget_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
