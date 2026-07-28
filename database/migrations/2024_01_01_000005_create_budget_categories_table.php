<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_anggaran_id')->constrained('anggaran_tahun');
            $table->foreignId('master_komponen_id')->constrained('master_komponen');
            $table->string('uraian'); // detail akun, free text. Terikat ke komponen & tahun saat dibuat (histori tidak berubah)
            $table->decimal('pagu_anggaran', 18, 2);
            $table->decimal('total_terpakai', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_categories');
    }
};
