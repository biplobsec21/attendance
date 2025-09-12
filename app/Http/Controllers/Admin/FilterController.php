<?php
// app/Http/Controllers/Admin/FilterController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filter;
use App\Models\FilterItem;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function index()
    {
        $filters = Filter::with('items')->paginate(20);
        return view('mpm.page.admin.filters.index', compact('filters'));
    }

    public function create()
    {
        return view('mpm.page.admin.filters.create');
    }

    public function store(Request $request)
    {
        $filter = Filter::create($request->only(['name', 'description']));

        if ($request->has('fields')) {
            foreach ($request->fields as $field) {
                FilterItem::create([
                    'filter_id'   => $filter->id,
                    'label'       => $field['label'],
                    'table_name'  => $field['table_name'],
                    'column_name' => $field['column_name'],
                    'operator'    => $field['operator'] ?? '=',
                    'value_type'  => $field['value_type'] ?? 'string',
                    'options'     => $field['options'] ?? null,
                ]);
            }
        }

        return redirect()->route('filters.index')->with('success', 'Filter created successfully');
    }

    public function edit(Filter $filter)
    {
        $filter->load('items');
        return view('mpm.page.admin.filters.edit', compact('filter'));
    }

    public function update(Request $request, Filter $filter)
    {
        // Update filter basic info
        $filter->update($request->only('name', 'description'));

        // Remove old items
        $filter->items()->delete();

        // Add new items from request
        if ($request->has('fields')) {
            foreach ($request->fields as $field) {
                $filter->items()->create([
                    'label'       => $field['label'],
                    'table_name'  => $field['table_name'],
                    'column_name' => $field['column_name'],
                    'operator'    => $field['operator'] ?? '=',
                    'value_type'  => $field['value_type'] ?? 'string',
                    'options'     => $field['options'] ?? null,
                ]);
            }
        }

        return redirect()->route('filters.index')->with('success', 'Filter updated successfully');
    }

    public function destroy(Filter $filter)
    {
        $filter->delete();
        return redirect()->route('filters.index')->with('success', 'Filter deleted.');
    }
}
