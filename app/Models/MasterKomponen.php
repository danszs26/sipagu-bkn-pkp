<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKomponen extends Model
{
    protected $table = 'master_komponen';

    protected $fillable = ['kode', 'nama_komponen', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function budgetCategories()
    {
        return $this->hasMany(BudgetCategory::class, 'master_komponen_id');
    }
}
