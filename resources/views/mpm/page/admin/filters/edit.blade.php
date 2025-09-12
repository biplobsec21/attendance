@extends('mpm.layouts.app')
@section('title', 'Edit Filter')
@section('content')
    <div class="container mx-auto p-6" x-data="filterBuilder({{ $filter->items->toJson() }})">

        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />

        @include('mpm.components.alerts')

        <h1 class="text-2xl font-bold mb-4">Edit Filter</h1>

        <form action="{{ route('filters.update', $filter->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Info -->
            <div class="bg-white shadow-md rounded-lg p-4 space-y-4">
                <div>
                    <label class="block font-semibold">Filter Name</label>
                    <input type="text" name="name" value="{{ $filter->name }}"
                        class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-200" required>
                </div>
                <div>
                    <label class="block font-semibold">Description</label>
                    <textarea name="description" class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-200">{{ $filter->description }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-4">
                <!-- Palette -->
                <div class="col-span-4 bg-gray-50 p-4 rounded-lg shadow">
                    <h2 class="font-bold mb-2">Available Fields</h2>
                    <div class="space-y-2">
                        <template x-for="field in availableFields" :key="field.table + '.' + field.name">
                            <div class="cursor-move bg-white border rounded-lg p-2 shadow-sm hover:bg-blue-50"
                                draggable="true" @dragstart="dragField(field)">
                                <span x-text="field.table + '.' + field.label"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Drop Zone -->
                <div class="col-span-8 bg-white p-4 rounded-lg shadow min-h-[300px] border-dashed border-2 border-gray-300"
                    @dragover.prevent @drop="dropField">
                    <h2 class="font-bold mb-2">Filter Fields</h2>
                    <template x-if="formFields.length === 0">
                        <p class="text-gray-400">Drag fields here...</p>
                    </template>

                    <div class="space-y-4">
                        <template x-for="(field, index) in formFields" :key="index">
                            <div class="p-3 bg-gray-50 border rounded-lg">
                                <!-- Hidden inputs -->
                                <input type="hidden" :name="'fields[' + index + '][table_name]'" :value="field.table">
                                <input type="hidden" :name="'fields[' + index + '][column_name]'" :value="field.name">
                                <input type="hidden" :name="'fields[' + index + '][label]'" :value="field.label">

                                <label class="block font-semibold" x-text="field.label"></label>

                                <div class="flex flex-wrap gap-2 mt-1">
                                    <!-- Operator -->
                                    <select :name="'fields[' + index + '][operator]'" class="border rounded p-1"
                                        x-model="field.operator">
                                        <option value="=">=</option>
                                        <option value="!=">≠</option>
                                        <option value="LIKE">Contains</option>
                                        <option value=">">></option>
                                        <option value="<">
                                            << /option>
                                        <option value="BETWEEN">Between</option>
                                    </select>

                                    <!-- Value Type -->
                                    <select :name="'fields[' + index + '][value_type]'" class="border rounded p-1"
                                        x-model="field.value_type">
                                        <option value="string">String</option>
                                        <option value="number">Number</option>
                                        <option value="date">Date</option>
                                        <option value="select">Select</option>
                                    </select>

                                    <!-- Options -->
                                    <input type="text" :name="'fields[' + index + '][options]'"
                                        class="flex-1 border rounded p-1" placeholder="Options (comma separated)"
                                        x-model="field.options">
                                </div>

                                <button type="button" class="text-red-500 mt-2" @click="removeField(index)">✕
                                    Remove</button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700">
                    Update Filter
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function filterBuilder(existingFields = []) {
            return {
                availableFields: [{
                        table: 'soldiers',
                        name: 'full_name',
                        label: 'Full Name'
                    },
                    {
                        table: 'soldiers',
                        name: 'army_no',
                        label: 'Army No'
                    },
                    {
                        table: 'soldiers',
                        name: 'gender',
                        label: 'Gender'
                    },
                    {
                        table: 'soldiers',
                        name: 'marital_status',
                        label: 'Marital Status'
                    },
                    {
                        table: 'soldiers',
                        name: 'mobile',
                        label: 'Mobile No'
                    },
                    {
                        table: 'soldiers',
                        name: 'district',
                        label: 'District'
                    },
                    {
                        table: 'soldiers',
                        name: 'is_leave',
                        label: 'Is Present'
                    },
                    {
                        table: 'ranks',
                        name: 'name',
                        label: 'Rank'
                    },
                    {
                        table: 'companies',
                        name: 'name',
                        label: 'Company'
                    },
                    {
                        table: 'soldier_educations',
                        name: 'passing_year',
                        label: 'Passing Year'
                    },
                    {
                        table: 'soldier_educations',
                        name: 'result',
                        label: 'Education Result'
                    },
                    {
                        table: 'educations',
                        name: 'name',
                        label: 'Exam Name'
                    },
                    {
                        table: 'cadres',
                        name: 'name',
                        label: 'Cadre'
                    },
                    {
                        table: 'soldier_services',
                        name: 'name',
                        label: 'Service Name'
                    },
                    {
                        table: 'skills',
                        name: 'name',
                        label: 'Skill Name'
                    },
                    {
                        table: 'atts',
                        name: 'name',
                        label: 'Att Name'
                    },
                    {
                        table: 'eres',
                        name: 'name',
                        label: 'ERE Name'
                    },
                    {
                        table: 'duties',
                        name: 'duty_name',
                        label: 'Duty Name'
                    },
                    {
                        table: 'medical_categories',
                        name: 'name',
                        label: 'Medical Category'
                    }
                ],
                formFields: existingFields.length > 0 ? existingFields.map(f => ({
                    table: f.table_name,
                    name: f.column_name,
                    label: f.label,
                    operator: f.operator,
                    value_type: f.value_type,
                    options: f.options
                })) : [],
                draggedField: null,

                dragField(field) {
                    this.draggedField = field
                },
                dropField() {
                    if (this.draggedField) {
                        this.formFields.push({
                            ...this.draggedField,
                            operator: '=',
                            value_type: 'string',
                            options: ''
                        });
                        this.draggedField = null;
                    }
                },
                removeField(index) {
                    this.formFields.splice(index, 1)
                }
            }
        }
    </script>
@endsection
