<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Export\ExportService;

class SoldierExportController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function export(Request $request)
    {
        return $this->exportService->exportSoldiers($request);
    }
}
