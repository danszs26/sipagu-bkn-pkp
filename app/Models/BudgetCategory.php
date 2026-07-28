<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetCategory extends Model
{
    protected $fillable = [
        'tahun_anggaran_id', 'master_komponen_id', 'uraian',
        'pagu_anggaran', 'total_terpakai', 'keterangan', 'is_active', 'created_by',
    ];

    protected $casts = [
        'pagu_anggaran' => 'decimal:2',
        'total_terpakai' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function anggaranTahun()
    {
        return $this->belongsTo(AnggaranTahun::class, 'tahun_anggaran_id');
    }

    public function masterKomponen()
    {
        return $this->belongsTo(MasterKomponen::class, 'master_komponen_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getSisaAnggaranAttribute(): float
    {
        return (float) $this->pagu_anggaran - (float) $this->total_terpakai;
    }

    public function getPersentaseTerpakaiAttribute(): float
    {
        if ((float) $this->pagu_anggaran <= 0) return 0;
        return round(((float) $this->total_terpakai / (float) $this->pagu_anggaran) * 100, 2);
    }

    /**
     * Cek apakah nominal pengeluaran tertentu masih muat dalam sisa pagu.
     * $excludeAmount dipakai saat edit transaksi (nominal lama dikeluarkan dulu dari perhitungan).
     */
    public function bisaMenampung(float $nominal, float $excludeAmount = 0): bool
    {
        $sisaSetelahExclude = (float) $this->total_terpakai - $excludeAmount;
        return ($sisaSetelahExclude + $nominal) <= (float) $this->pagu_anggaran;
    }
}
