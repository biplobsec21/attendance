<?php
// database/seeders/FilterSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Filter;
use App\Models\FilterItem;

class FilterSeeder extends Seeder
{
    public function run(): void
    {
        $filter = Filter::create([
            'name' => 'Basic Soldier Filter',
            'description' => 'Filter soldiers by basic personal info',
            'created_by' => 1, // admin id
        ]);

        FilterItem::insert([
            [
                'filter_id' => $filter->id,
                'table_name' => 'soldiers',
                'column_name' => 'blood_group',
                'operator' => '=',
                'value_type' => 'select',
                'label' => 'Blood Group',
                'options' => json_encode(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
            ],
            [
                'filter_id' => $filter->id,
                'table_name' => 'soldiers',
                'column_name' => 'gender',
                'operator' => '=',
                'value_type' => 'select',
                'label' => 'Gender',
                'options' => json_encode(['Male', 'Female']),
            ],
            [
                'filter_id' => $filter->id,
                'table_name' => 'soldiers',
                'column_name' => 'rank_id',
                'operator' => '=',
                'value_type' => 'select',
                'label' => 'Rank',
                'options' => json_encode([]), // can be populated from ranks table dynamically
            ],
        ]);
    }
}
