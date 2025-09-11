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
        $filters = Filter::with('items')->paginate(10);
        // dd($filters);
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
                dd($field);
                FilterItem::create([
                    'filter_id'  => $filter->id,
                    'table_name' => $field['table_name'],   // e.g., "soldiers"
                    'field_name' => $field['field_name'],    // e.g., "gender"
                    'operator'   => $field['operator'], // e.g., "="
                    'value'      => $field['value'],   // e.g., "Male"
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
        if ($request->has('add_item')) {
            $filter->items()->create([
                'label'       => $request->label,
                'table_name'  => $request->table_name,
                'column_name' => $request->column_name,
                'operator'    => $request->operator,
                'value_type'  => $request->value_type,
                'options'     => $request->options ? explode(',', $request->options) : null,
            ]);

            return back()->with('success', 'Filter item added successfully.');
        }

        // Otherwise, update filter info
        $filter->update($request->only('name', 'description'));
        return back()->with('success', 'Filter updated successfully.');
    }


    public function destroy(Filter $filter)
    {
        $filter->delete();
        return redirect()->route('filters.index')->with('success', 'Filter deleted.');
    }
}
