<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Rank;
use App\Models\CompanyRankManpower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyRankManpowerController extends Controller
{
    public function index()
    {
        $companies = Company::active()->orderBy('name')->get();
        $ranks = Rank::active()->orderBy('id')->get();

        // Separate officer ranks from other ranks
        $officerRanks = $ranks->filter(function ($rank) {
            return $rank->type === 'OFFICER';
        });

        $otherRanks = $ranks->filter(function ($rank) {
            return $rank->type !== 'OFFICER';
        });

        // Fetch existing manpower data
        $manpower = CompanyRankManpower::get()
            ->groupBy('company_id')
            ->map(function ($rows) {
                return $rows->keyBy('rank_id');
            });

        // Calculate officer totals for each company
        $officerTotals = [];
        foreach ($companies as $company) {
            $officerTotal = 0;
            foreach ($officerRanks as $rank) {
                if (isset($manpower[$company->id][$rank->id])) {
                    $officerTotal += $manpower[$company->id][$rank->id]->manpower_number;
                }
            }
            $officerTotals[$company->id] = $officerTotal;
        }

        return view('mpm.page.company_rank_manpower.index', compact('companies', 'officerRanks', 'otherRanks', 'manpower', 'officerTotals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'manpower' => ['required', 'array'],
            'manpower.*' => ['array'],
            'manpower.*.*' => ['nullable', 'integer', 'min:0'],
            'officer_manpower' => ['required', 'array'],
            'officer_manpower.*' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            // Get all officer ranks once
            $officerRanks = Rank::active()->where('type', 'OFFICER')->get();
            $primaryOfficerRank = $officerRanks->first();

            // Process each company
            foreach ($data['officer_manpower'] as $company_id => $officerTotal) {
                $officerTotal = $officerTotal ?? 0;

                // Save non-officer ranks for this company
                if (isset($data['manpower'][$company_id])) {
                    foreach ($data['manpower'][$company_id] as $rank_id => $manpower_number) {
                        $manpower_number = $manpower_number ?? 0;
                        CompanyRankManpower::updateOrCreate(
                            ['company_id' => $company_id, 'rank_id' => $rank_id],
                            ['manpower_number' => $manpower_number]
                        );
                    }
                }

                // Handle officer manpower
                if ($primaryOfficerRank) {
                    // Store the total in the first officer rank
                    CompanyRankManpower::updateOrCreate(
                        ['company_id' => $company_id, 'rank_id' => $primaryOfficerRank->id],
                        ['manpower_number' => $officerTotal]
                    );

                    // Set all other officer ranks to 0 (if there are multiple officer ranks)
                    foreach ($officerRanks->skip(1) as $rank) {
                        CompanyRankManpower::updateOrCreate(
                            ['company_id' => $company_id, 'rank_id' => $rank->id],
                            ['manpower_number' => 0]
                        );
                    }
                }
            }

            DB::commit();

            return redirect()->route('company_rank_manpower.index')
                ->with('success', 'Manpower distribution updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to update manpower distribution: ' . $e->getMessage())
                ->withInput();
        }
    }
}
