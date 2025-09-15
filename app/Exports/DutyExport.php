<?php

namespace App\Exports;

use App\Models\SoldierDuty;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DutyExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = SoldierDuty::select(
            'assigned_date',
            'duty_id',
            'soldier_id',
            'start_time',
            'end_time',
            'status',
            'remarks'
        )->with('duty:id,duty_name');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('assigned_date', [$this->startDate, $this->endDate]);
        } elseif ($this->startDate) {
            $query->where('assigned_date', $this->startDate);
        }

        return $query->get()->map(function ($item) {
            return [
                'Assigned Date' => $item->assigned_date,
                'Duty Name'     => $item->duty->duty_name ?? 'N/A',
                'Soldier ID'    => $item->soldier_id,
                'Start Time'    => $item->start_time,
                'End Time'      => $item->end_time,
                'Status'        => $item->status,
                'Remarks'       => $item->remarks,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Assigned Date',
            'Duty Name',
            'Soldier ID',
            'Start Time',
            'End Time',
            'Status',
            'Remarks',
        ];
    }
}
