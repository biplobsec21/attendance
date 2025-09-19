<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointments = [
            'Duty Officer',
            'Duty JCO',
            'Duty NCO',
            'Duty Clerk',
            'DR',
            'Runner',
            'Last Duty',
            'Quarter Guard Duty',
            'RP Duty',
            'Line Sick',
            'PPGF',
            'Field Mess',
            'Soldier\'s Mess Cook',
            'CQ/Store Man',
            'Magistrate NCO',
            'Canteen',
            'Battalion Working',
            'Battalion Office',
            'Fresh Worker',
            'Mosque / RCO',
            'Going on Leave',
            'INT',
            'Instructor',
            'Fire',
            'NC (E)',
            'NC (U)',
            'MT Assignment',
            'Milk NCO',
            'JCO Mess (Storekeeper)',
            'Cut The Grass',
            'IPFT',
            'IPFT Working',
            'Group / Team',
            'Animal Husbandry',
            'Project NCO',
            'Fishery / Fish Farming',
            'Market (Marketing duty)',
            'Medical Assistant',
            'Signal',
            'Carpenter',
            'Gardener / Garden Worker',
            'Food Collection',
            'ArtDoc Worker',
        ];

        foreach ($appointments as $appointment) {
            DB::table('appointments')->insert([
                'name' => $appointment,
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
