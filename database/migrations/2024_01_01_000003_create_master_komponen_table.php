<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_komponen', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // F, G, H, I, M, Q, R, dst
            $table->string('nama_komponen')->nullable(); // label opsional, ex: "Layanan Rumah Tangga"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_komponen');
    }
};
