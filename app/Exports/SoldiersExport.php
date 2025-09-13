<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SoldiersExport implements FromView
{
    protected $soldiers;

    public function __construct($soldiers)
    {
        $this->soldiers = $soldiers;
    }

    public function view(): View
    {
        // You can create a Blade view for Excel export
        return view('mpm.page.exports.soldiers', [
            'soldiers' => $this->soldiers
        ]);
    }
}
