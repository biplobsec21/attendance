<?php
// app/Models/Filter.php
namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use LogsAllActivity;
    protected $fillable = ['name', 'description', 'created_by'];

    public function items()
    {
        return $this->hasMany(FilterItem::class);
    }
}
