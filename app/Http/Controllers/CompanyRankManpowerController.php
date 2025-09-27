<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Rank;
use App\Models\CompanyRankManpower;
use Illuminate\Http\Request;

class CompanyRankManpowerController extends Controller
{
    public function index()
    {
        $companies = Company::active()->orderBy('name')->get();
        $ranks = Rank::active()->orderBy('name')->get();

        // Fetch existing manpower data
        $manpower = CompanyRankManpower::get()
            ->groupBy('company_id')
            ->map(function ($rows) {
                return $rows->keyBy('rank_id');
            });

        return view('mpm.page.company_rank_manpower.index', compact('companies', 'ranks', 'manpower'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'manpower' => ['required', 'array'],
            'manpower.*' => ['array'],
            'manpower.*.*' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($data['manpower'] as $company_id => $ranks) {
            foreach ($ranks as $rank_id => $manpower_number) {
                $manpower_number = $manpower_number ?? 0;
                CompanyRankManpower::updateOrCreate(
                    ['company_id' => $company_id, 'rank_id' => $rank_id],
                    ['manpower_number' => $manpower_number]
                );
            }
        }

        return redirect()->back()->with('success', 'Manpower distribution updated.');
    }
}
