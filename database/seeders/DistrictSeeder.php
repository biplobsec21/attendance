<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\District;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            'Bagerhat',
            'Bandarban',
            'Barguna',
            'Barishal',
            'Bhola',
            'Bogura',
            'Brahmanbaria',
            'Chandpur',
            'Chapai Nawabganj',
            'Chattogram',
            'Chuadanga',
            'Cox\'s Bazar',
            'Cumilla',
            'Dhaka',
            'Dinajpur',
            'Faridpur',
            'Feni',
            'Gaibandha',
            'Gazipur',
            'Gopalganj',
            'Habiganj',
            'Jamalpur',
            'Jashore',
            'Jhalokati',
            'Jhenaidah',
            'Joypurhat',
            'Khagrachhari',
            'Khulna',
            'Kishoreganj',
            'Kurigram',
            'Kushtia',
            'Lakshmipur',
            'Lalmonirhat',
            'Madaripur',
            'Magura',
            'Manikganj',
            'Meherpur',
            'Moulvibazar',
            'Munshiganj',
            'Mymensingh',
            'Naogaon',
            'Narail',
            'Narayanganj',
            'Narsingdi',
            'Natore',
            'Netrokona',
            'Nilphamari',
            'Noakhali',
            'Pabna',
            'Panchagarh',
            'Patuakhali',
            'Pirojpur',
            'Rajbari',
            'Rajshahi',
            'Rangamati',
            'Rangpur',
            'Satkhira',
            'Shariatpur',
            'Sherpur',
            'Sirajganj',
            'Sunamganj',
            'Sylhet',
            'Tangail',
            'Thakurgaon',
        ];

        foreach ($districts as $name) {
            District::firstOrCreate(['name' => $name]);
        }
    }
}





