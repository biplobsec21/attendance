<?php

namespace App\Services\Export;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfExporter
{
    public function export($soldiers)
    {
        $pdf = Pdf::loadView('mpm.page.exports.soldiers', ['soldiers' => $soldiers]);

        $filename = 'soldiers_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }
}
