<?php

namespace App\Services\Export;

use App\Repositories\SoldierRepository;
use App\Exports\SoldiersExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportService
{
    protected $repository;

    public function __construct(SoldierRepository $repository)
    {
        $this->repository = $repository;
    }

    public function exportSoldiers($request)
    {
        $format = $request->get('format', 'excel');
        $selectedIds = $request->get('selected') ? explode(',', $request->get('selected')) : [];

        $soldiers = $this->repository->getFilteredSoldiers($request, $selectedIds);

        return match ($format) {
            'excel' => $this->exportToExcel($soldiers),
            'pdf'   => app(PdfExporter::class)->export($soldiers),
            default => throw new \Exception("Invalid format: $format")
        };
    }

    private function exportToExcel($soldiers)
    {
        $filename = 'soldiers_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new SoldiersExport($soldiers), $filename);
    }

    // helper methods
    public function calculateProfileCompletion($soldier)
    {
        // your logic here
        return 80; // example
    }

    public function getStatusText($soldier)
    {
        // your logic here
        return $soldier->status ? 'Active' : 'Inactive';
    }
}
