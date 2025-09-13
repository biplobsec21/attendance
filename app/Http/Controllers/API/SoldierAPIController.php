<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller; // <- Add this line

use Illuminate\Http\Request;
use App\Models\Soldier;
use Illuminate\Support\Facades\DB;
use App\Services\SoldierDataFormatter;

class SoldierAPIController extends Controller
{
    protected $formatter;

    public function __construct(SoldierDataFormatter $formatter)
    {
        $this->formatter = $formatter;
    }
    public function index(Request $request)
    {


        $profiles = Soldier::with(['rank', 'company'])->get();

        return response()->json([
            'data' => $this->formatter->formatCollection($profiles),
            'stats' => [
                'total' => $profiles->count(),
                'active' => $profiles->where('is_leave', false)->where('is_sick', false)->count(),
                'leave' => $profiles->where('is_leave', true)->count(),
                'medical' => $profiles->where('is_sick', true)->count()
            ]
        ]);
    }
}
