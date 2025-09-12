<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_name',
        'action',
        'previous_data',
        'changed_data',
        'user_id',
    ];

    protected $casts = [
        'previous_data' => 'array',
        'changed_data'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
