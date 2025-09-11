<?php
// app/Models/Filter.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    protected $fillable = ['name', 'description', 'created_by'];

    public function items()
    {
        return $this->hasMany(FilterItem::class);
    }
}
