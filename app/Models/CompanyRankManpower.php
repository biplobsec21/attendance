<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyRankManpower extends Model
{
    protected $table = 'company_rank_manpower';

    protected $fillable = [
        'company_id',
        'rank_id',
        'manpower_number',
    ];
}
