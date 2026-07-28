<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pejabat_penandatangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');       // termasuk gelar, ex: "Eko Nugroho, S.Psi."
            $table->string('jabatan');    // ex: "Kepala UPT BKN Pangkalpinang"
            $table->string('nip')->nullable();
            $table->boolean('is_active')->default(true); // hanya satu yang aktif dipakai di laporan
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pejabat_penandatangan');
    }
};
