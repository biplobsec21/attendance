<?php

namespace App\Repositories;

use App\Models\Soldier;
use Illuminate\Http\Request;

class SoldierRepository
{
    public function getFilteredSoldiers(Request $request, array $selectedIds = [])
    {
        $query = Soldier::with(['rank', 'company']);

        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('army_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('rank')) {
            $query->whereHas('rank', function ($q) use ($request) {
                $q->where('name', $request->get('rank'));
            });
        }

        if ($request->filled('company')) {
            $query->whereHas('company', function ($q) use ($request) {
                $q->where('name', $request->get('company'));
            });
        }
        
        return $query->get();
    }
}
