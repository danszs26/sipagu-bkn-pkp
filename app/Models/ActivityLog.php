<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    const UPDATED_AT = null; // log tidak pernah diupdate, hanya insert

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'description', 'old_data', 'new_data', 'ip_address',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function catat(string $action, string $modelType, ?int $modelId, string $description, ?array $oldData = null, ?array $newData = null): void
    {
        static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()?->ip(),
        ]);
    }
}
