<?php

// app/Console/Commands/UpdateAppointmentStatuses.php
namespace App\Console\Commands;

use App\Models\SoldierServices;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateAppointmentStatuses extends Command
{
    protected $signature = 'appointments:update-statuses';
    protected $description = 'Update appointment statuses based on dates';

    public function handle()
    {
        $today = Carbon::today();

        // Update appointments that should be completed
        SoldierServices::where('status', '!=', 'completed')
            ->whereNotNull('appointments_to_date')
            ->where('appointments_to_date', '<', $today)
            ->update(['status' => 'completed']);

        // Update appointments that should be active
        SoldierServices::where('status', 'scheduled')
            ->where('appointments_from_date', '<=', $today)
            ->update(['status' => 'active']);

        $this->info('Appointment statuses updated successfully.');
    }
}
