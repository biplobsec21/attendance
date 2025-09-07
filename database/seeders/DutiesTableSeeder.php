<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DutiesTableSeeder extends Seeder
{
    public function run(): void
    {
        $duties = [
            ['duty_name' => 'Morning Guard',             'start_time' => '06:00:00', 'end_time' => '12:00:00', 'status' => 'Active'],
            ['duty_name' => 'Afternoon Guard',           'start_time' => '12:00:00', 'end_time' => '18:00:00', 'status' => 'Active'],
            ['duty_name' => 'Night Patrol',              'start_time' => '18:00:00', 'end_time' => '06:00:00', 'status' => 'Active'], // next day
            ['duty_name' => 'Admin Desk',                'start_time' => '09:00:00', 'end_time' => '17:00:00', 'status' => 'Active'],
            ['duty_name' => 'Gate Security',             'start_time' => '00:00:00', 'end_time' => '06:00:00', 'status' => 'Active'],
            ['duty_name' => 'Logistics Support',         'start_time' => '08:00:00', 'end_time' => '16:00:00', 'status' => 'Active'],
            ['duty_name' => 'Emergency Response',        'start_time' => '18:00:00', 'end_time' => '00:00:00', 'status' => 'Active'], // next day
            ['duty_name' => 'Medical Post',              'start_time' => '07:00:00', 'end_time' => '19:00:00', 'status' => 'Active'],
            ['duty_name' => 'Canteen Duty',              'start_time' => '08:00:00', 'end_time' => '14:00:00', 'status' => 'Active'],
            ['duty_name' => 'Vehicle Checkpoint',        'start_time' => '06:00:00', 'end_time' => '12:00:00', 'status' => 'Active'],
            ['duty_name' => 'Evening Vehicle Check',     'start_time' => '12:00:00', 'end_time' => '18:00:00', 'status' => 'Active'],
            ['duty_name' => 'Night Vehicle Check',       'start_time' => '18:00:00', 'end_time' => '00:00:00', 'status' => 'Active'], // next day
            ['duty_name' => 'Overnight Vehicle Check',   'start_time' => '00:00:00', 'end_time' => '06:00:00', 'status' => 'Active'],
            ['duty_name' => 'Training Supervision',      'start_time' => '06:00:00', 'end_time' => '10:00:00', 'status' => 'Active'],
            ['duty_name' => 'Parade Ground Supervision', 'start_time' => '10:00:00', 'end_time' => '14:00:00', 'status' => 'Active'],
            ['duty_name' => 'IT & Communication',        'start_time' => '09:00:00', 'end_time' => '17:00:00', 'status' => 'Active'],
            ['duty_name' => 'Quarter Guard',             'start_time' => '00:00:00', 'end_time' => '06:00:00', 'status' => 'Active'],
            ['duty_name' => 'Armory Duty',               'start_time' => '08:00:00', 'end_time' => '16:00:00', 'status' => 'Active'],
        ];



        foreach ($duties as $duty) {
            DB::table('duties')->insert(array_merge($duty, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
