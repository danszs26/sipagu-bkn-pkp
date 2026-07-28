<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'no_referensi', 'tanggal', 'budget_category_id', 'vendor_id', 'nominal',
        'uraian', 'bukti_file_path', 'bukti_file_original_name',
        'idempotency_token', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function budgetCategory()
    {
        return $this->belongsTo(BudgetCategory::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Generate nomor referensi otomatis: TRX-YYYYMMDD-XXXX (reset urutan tiap hari)
     */
    public static function generateNoReferensi(\DateTimeInterface $tanggal): string
    {
        $prefix = 'TRX-' . $tanggal->format('Ymd') . '-';
        $countToday = static::withTrashed()
            ->where('no_referensi', 'like', $prefix . '%')
            ->count();

        return $prefix . str_pad((string) ($countToday + 1), 4, '0', STR_PAD_LEFT);
    }
}
