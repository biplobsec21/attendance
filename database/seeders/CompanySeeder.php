<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $companies = [
            'A',
            'B',
            'C',
            'D',
            'Shadar',
            'Joined',
        ];

        foreach ($companies as $company) {
            Company::create(['name' => $company]);
        }
    }
}
