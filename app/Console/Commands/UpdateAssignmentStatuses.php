<?php

namespace App\Console\Commands;

use App\Models\SoldierCourse;
use App\Models\SoldierCadre;
use Illuminate\Console\Command;

class UpdateAssignmentStatuses extends Command
{
    protected $signature = 'assignments:update-statuses';
    protected $description = 'Update statuses for all course and cadre assignments';

    public function handle()
    {
        $this->info('Updating course statuses...');
        $courses = SoldierCourse::all();
        foreach ($courses as $course) {
            $course->updateStatus();
        }
        $this->info("Updated {$courses->count()} courses.");

        $this->info('Updating cadre statuses...');
        $cadres = SoldierCadre::all();
        foreach ($cadres as $cadre) {
            $cadre->updateStatus();
        }
        $this->info("Updated {$cadres->count()} cadres.");

        $this->info('All assignment statuses have been updated.');
        return 0;
    }
}
