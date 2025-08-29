<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $companies = [
            'Alpha Company',
            'Bravo Company',
            'Charlie Company',
            'Delta Company',
            'Echo Company',
            'Foxtrot Company',
        ];

        foreach ($companies as $company) {
            Company::create(['name' => $company]);
        }
    }
}







