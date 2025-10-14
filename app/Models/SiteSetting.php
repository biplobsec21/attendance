<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsAllActivity;

class SiteSetting extends Model
{
    use HasFactory, LogsAllActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'site_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_name',
        'pt_time',
        'games_time',
        'parade_time',
        'roll_call_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pt_time' => 'datetime:H:i',
        'games_time' => 'datetime:H:i',
        'parade_time' => 'datetime:H:i',
        'roll_call_time' => 'datetime:H:i',
    ];

    /**
     * Get the singleton instance of site settings
     */
    public static function getSettings()
    {
        return static::firstOrCreate([], [
            'site_name' => 'Your Site Name',
            'pt_time' => '06:00:00',
            'games_time' => '16:00:00',
            'parade_time' => '08:00:00',
            'roll_call_time' => '07:30:00',
        ]);
    }
}
