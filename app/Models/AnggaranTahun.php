<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggaranTahun extends Model
{
    protected $table = 'anggaran_tahun';

    protected $fillable = ['tahun', 'is_active', 'created_by'];

    protected $casts = ['is_active' => 'boolean'];

    public function budgetCategories()
    {
        return $this->hasMany(BudgetCategory::class, 'tahun_anggaran_id');
    }
}
