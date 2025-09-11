<?php
// app/Http/Controllers/FilterController.php
namespace App\Http\Controllers;

use App\Models\Filter;

class FilterController extends Controller
{
    public function index()
    {
        return Filter::with('items')->get();
    }
    public function apply(Request $request)
    {
        $request->validate([
            'filter_id' => 'required|exists:filters,id',
            'values'    => 'required|array'
        ]);

        $filter = Filter::with('items')->findOrFail($request->filter_id);
        $values = $request->values;

        $query = Soldier::query();

        foreach ($filter->items as $item) {
            if (!isset($values[$item->column_name]) || $values[$item->column_name] === null) {
                continue; // skip if no value provided
            }

            $value = $values[$item->column_name];

            switch ($item->table_name) {
                case 'soldiers':
                    $query->where($item->column_name, $item->operator, $value);
                    break;

                case 'soldier_courses':
                    $query->whereHas('courses', function ($q) use ($item, $value) {
                        $q->where($item->column_name, $item->operator, $value);
                    });
                    break;

                case 'soldier_services':
                    $query->whereHas('services', function ($q) use ($item, $value) {
                        $q->where($item->column_name, $item->operator, $value);
                    });
                    break;

                case 'soldier_cadres':
                    $query->whereHas('cadres', function ($q) use ($item, $value) {
                        $q->where($item->column_name, $item->operator, $value);
                    });
                    break;

                case 'soldier_educations':
                    $query->whereHas('educations', function ($q) use ($item, $value) {
                        $q->where($item->column_name, $item->operator, $value);
                    });
                    break;

                case 'soldier_skills':
                    $query->whereHas('skills', function ($q) use ($item, $value) {
                        $q->where($item->column_name, $item->operator, $value);
                    });
                    break;

                case 'soldiers_medical':
                    $query->whereHas('medical', function ($q) use ($item, $value) {
                        $q->where($item->column_name, $item->operator, $value);
                    });
                    break;

                default:
                    // ignore if not mapped yet
                    break;
            }
        }

        // Eager load relations if you want to show details
        $soldiers = $query->with(['rank', 'company'])->paginate(20);

        return response()->json($soldiers);
    }
}
