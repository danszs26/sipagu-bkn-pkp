<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = ['nama_vendor', 'keterangan', 'is_active', 'created_by'];

    protected $casts = ['is_active' => 'boolean'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
