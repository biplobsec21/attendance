<?php
// app/Models/FilterItem.php
namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;

class FilterItem extends Model
{
    use LogsAllActivity;
    protected $fillable = [
        'filter_id',
        'table_name',
        'column_name',
        'operator',
        'value_type',
        'label',
        'options'
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function filter()
    {
        return $this->belongsTo(Filter::class);
    }
}
