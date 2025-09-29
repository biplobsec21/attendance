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

        // Save non-officer ranks
        foreach ($data['manpower'] as $company_id => $ranks) {
            foreach ($ranks as $rank_id => $manpower_number) {
                $manpower_number = $manpower_number ?? 0;
                CompanyRankManpower::updateOrCreate(
                    ['company_id' => $company_id, 'rank_id' => $rank_id],
                    ['manpower_number' => $manpower_number]
                );
            }
        }

        // Distribute officer manpower among officer ranks
        foreach ($data['officer_manpower'] as $company_id => $total_officers) {
            $total_officers = $total_officers ?? 0;

            // Get all officer ranks for this company
            $companyOfficerRanks = Rank::active()->where('type', 'OFFICER')->get();

            // Get existing manpower distribution for officer ranks
            $existingOfficerManpower = CompanyRankManpower::where('company_id', $company_id)
                ->whereIn('rank_id', $companyOfficerRanks->pluck('id'))
                ->get()
                ->keyBy('rank_id');

            // Calculate total existing officers
            $existingTotal = $existingOfficerManpower->sum('manpower_number');

            // If no existing data, distribute equally
            if ($existingTotal == 0) {
                $officerCount = $companyOfficerRanks->count();
                if ($officerCount > 0) {
                    $baseValue = floor($total_officers / $officerCount);
                    $remainder = $total_officers % $officerCount;

                    foreach ($companyOfficerRanks as $index => $rank) {
                        $value = $baseValue + ($index < $remainder ? 1 : 0);
                        CompanyRankManpower::updateOrCreate(
                            ['company_id' => $company_id, 'rank_id' => $rank->id],
                            ['manpower_number' => $value]
                        );
                    }
                }
            } else {
                // Distribute proportionally based on existing distribution
                foreach ($companyOfficerRanks as $rank) {
                    $existingValue = $existingOfficerManpower[$rank->id]->manpower_number ?? 0;
                    $ratio = $existingTotal > 0 ? $existingValue / $existingTotal : 0;
                    $newValue = round($total_officers * $ratio);

                    CompanyRankManpower::updateOrCreate(
                        ['company_id' => $company_id, 'rank_id' => $rank->id],
                        ['manpower_number' => $newValue]
                    );
                }
            }
        }

        return redirect()->back()->with('success', 'Manpower distribution updated.');
    }
}
