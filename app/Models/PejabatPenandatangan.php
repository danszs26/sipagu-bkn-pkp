<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PejabatPenandatangan extends Model
{
    protected $table = 'pejabat_penandatangan';

    protected $fillable = ['nama', 'jabatan', 'nip', 'is_active', 'created_by'];

    protected $casts = ['is_active' => 'boolean'];
}
