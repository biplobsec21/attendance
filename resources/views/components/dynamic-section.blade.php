@extends('mpm.layouts.app')

@section('title', 'Create Qualification')

@section('content')
    <x-profile-step-nav :steps="$profileSteps" />

    <main class="container mx-auto p-6">
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-800">Qualifications & Activities</h1>
            <p class="text-gray-500">List your academic background, courses, and other professional qualifications.</p>
        </div>

        <div class="bg-white border rounded-lg p-8 space-y-10">
            <form action="{{ route('profile.saveQualifications', $profile->id) }}" method="post">
                @csrf

                {{-- Education --}}
                <x-dynamic-section title="Education" description="Add your academic qualifications." :options="$educations"
                    type="education" :fields="['status' => ['Running', 'Passed'], 'year' => 'Year']" />

                {{-- Courses --}}
                <x-dynamic-section title="Courses" description="List any professional courses." :options="$courses"
                    type="course" :fields="[
                        'status' => ['Running', 'Passed'],
                        'start_date' => 'Start Date',
                        'end_date' => 'End Date',
                        'result' => 'Result',
                    ]" />

                {{-- Cadres --}}
                <x-dynamic-section title="Cadres" description="List any professional cadres." :options="$cadres"
                    type="cadre" :fields="[
                        'status' => ['Running', 'Passed'],
                        'start_date' => 'Start Date',
                        'end_date' => 'End Date',
                        'result' => 'Result',
                    ]" />

                {{-- Co-Curricular Activities --}}
                <x-dynamic-section title="Co-Curricular Activities" description="Include sports, clubs, etc."
                    :options="$skills" type="cocurricular" :fields="['remark' => 'Achievement / Remark']" />

                {{-- ERE --}}
                <x-dynamic-section title="ERE" description="List your ERE history." :options="$eres" type="ere"
                    :fields="['start_date' => 'Start Date', 'end_date' => 'End Date']" />

                {{-- Attachments --}}
                <x-dynamic-section title="Attachments" description="List any attachments." :options="$atts" type="att"
                    :fields="['start_date' => 'Start Date', 'end_date' => 'End Date']" />

                {{-- Bottom nav --}}
                <div id="bottom-navigation" class="flex justify-between mt-6 border-t pt-6">
                    <button id="prev-btn" type="button"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded">
                        Previous
                    </button>
                    <button id="next-btn" type="submit"
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded">
                        Save &amp; Continue
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            function createRow(type, options, fields) {
                const row = document.createElement('div');
                row.className = 'grid grid-cols-1 sm:grid-cols-12 gap-2 items-center border p-3 rounded-md';

                // Name select
                const nameDiv = document.createElement('div');
                nameDiv.className = 'flex items-center sm:col-span-4';
                const nameSelect = document.createElement('select');
                nameSelect.className = 'w-full p-2 border rounded-md name-select bg-white';
                nameSelect.name = `${type}[][name]`;
                nameSelect.innerHTML =
                    `<option value="">Select ${type.charAt(0).toUpperCase() + type.slice(1)}</option>` +
                    options.map(o => `<option value="${o.id}">${o.name}</option>`).join('');
                nameDiv.appendChild(nameSelect);
                row.appendChild(nameDiv);

                // Dynamic fields
                for (const key in fields) {
                    if (Array.isArray(fields[key])) {
                        const statusDiv = document.createElement('div');
                        statusDiv.className = 'status-container hidden sm:col-span-3';
                        const statusSelect = document.createElement('select');
                        statusSelect.className = 'status-select w-full p-2 border rounded-md bg-white';
                        statusSelect.name = `${type}[][${key}]`;
                        statusSelect.innerHTML = `<option value="">Select Status</option>` +
                            fields[key].map(val => `<option value="${val}">${val}</option>`).join('');
                        statusDiv.appendChild(statusSelect);
                        row.appendChild(statusDiv);
                    } else if (key.includes('date') || key === 'year') {
                        const dateDiv = document.createElement('div');
                        dateDiv.className = key === 'year' ? 'year-container hidden sm:col-span-2' :
                            'dates-container hidden sm:col-span-2 grid grid-cols-2 gap-2';
                        if (key === 'year') {
                            const input = document.createElement('input');
                            input.type = 'text';
                            input.placeholder = fields[key];
                            input.name = `${type}[][${key}]`;
                            input.className = 'w-full p-2 border rounded-md';
                            dateDiv.appendChild(input);
                        } else {
                            const startInput = document.createElement('input');
                            startInput.type = 'date';
                            startInput.name = `${type}[][start_date]`;
                            startInput.className = 'w-full p-2 border rounded-md';
                            startInput.title = 'Start Date';
                            const endInput = document.createElement('input');
                            endInput.type = 'date';
                            endInput.name = `${type}[][end_date]`;
                            endInput.className = 'w-full p-2 border rounded-md';
                            endInput.title = 'End Date';
                            dateDiv.appendChild(startInput);
                            dateDiv.appendChild(endInput);
                        }
                        row.appendChild(dateDiv);
                    } else {
                        const remarkDiv = document.createElement('div');
                        remarkDiv.className = 'remark-container hidden sm:col-span-3';
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.placeholder = fields[key];
                        input.name = `${type}[][${key}]`;
                        input.className = 'w-full p-2 border rounded-md';
                        remarkDiv.appendChild(input);
                        row.appendChild(remarkDiv);
                    }
                }

                // Remove button
                const removeDiv = document.createElement('div');
                removeDiv.className = 'sm:col-span-1 flex justify-end';
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full';
                removeBtn.textContent = '✕';
                removeDiv.appendChild(removeBtn);
                row.appendChild(removeDiv);

                return row;
            }

            function addRow(type, options, fields) {
                const container = document.getElementById(`${type}-container`);
                if (!container) return;
                const row = createRow(type, options, fields);
                container.appendChild(row);
            }

            const sections = @json([
                [
                    'type' => 'education',
                    'options' => $educations,
                    'fields' => ['status' => ['Running', 'Passed'], 'year' => 'Year'],
                ],
                [
                    'type' => 'course',
                    'options' => $courses,
                    'fields' => [
                        'status' => ['Running', 'Passed'],
                        'start_date' => 'Start Date',
                        'end_date' => 'End Date',
                        'result' => 'Result',
                    ],
                ],
                [
                    'type' => 'cadre',
                    'options' => $cadres,
                    'fields' => [
                        'status' => ['Running', 'Passed'],
                        'start_date' => 'Start Date',
                        'end_date' => 'End Date',
                        'result' => 'Result',
                    ],
                ],
                ['type' => 'cocurricular', 'options' => $skills, 'fields' => ['remark' => 'Achievement / Remark']],
                ['type' => 'ere', 'options' => $eres, 'fields' => ['start_date' => 'Start Date', 'end_date' => 'End Date']],
                ['type' => 'att', 'options' => $atts, 'fields' => ['start_date' => 'Start Date', 'end_date' => 'End Date']],
            ]);

            sections.forEach(section => {
                if (document.getElementById(`${section.type}-container`).children.length === 0) {
                    addRow(section.type, section.options, section.fields);
                }
            });

            document.querySelectorAll(".add-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    const type = btn.id.replace("add-", "");
                    const section = sections.find(s => s.type === type);
                    if (section) addRow(type, section.options, section.fields);
                });
            });

            document.querySelector("main").addEventListener("click", e => {
                const rm = e.target.closest(".remove-row");
                if (rm) rm.closest(".grid")?.remove();
            });

            document.querySelector("main").addEventListener("change", e => {
                const row = e.target.closest(".grid");
                if (!row) return;
                const show = (el, on = true) => el && el.classList.toggle("hidden", !on);
                const hasValue = sel => sel && sel.value && sel.value !== "";
                if (e.target.matches(".name-select") || e.target.matches(".status-select")) {
                    const parentId = row.parentElement?.id || "";
                    if (parentId === "education-container") {
                        const nameSelect = row.querySelector(".name-select");
                        const statusWrap = row.querySelector(".status-container");
                        const statusSelect = row.querySelector(".status-select");
                        const yearContainer = row.querySelector(".year-container");
                        show(statusWrap, hasValue(nameSelect));
                        show(yearContainer, statusSelect && statusSelect.value === "Passed");
                        if (!hasValue(nameSelect)) show(yearContainer, false);
                    } else if (parentId === "course-container" || parentId === "cadre-container") {
                        const nameSelect = row.querySelector(".name-select");
                        const statusWrap = row.querySelector(".status-container");
                        const statusSelect = row.querySelector(".status-select");
                        const passedFields = row.querySelector(".passed-fields-container");
                        show(statusWrap, hasValue(nameSelect));
                        show(passedFields, statusSelect && statusSelect.value === "Passed");
                        if (!hasValue(nameSelect)) show(passedFields, false);
                    } else if (parentId === "cocurricular-container") {
                        const nameSelect = row.querySelector(".name-select");
                        const remarkWrap = row.querySelector(".remark-container");
                        show(remarkWrap, hasValue(nameSelect));
                    } else if (parentId === "ere-container" || parentId === "att-container") {
                        const nameSelect = row.querySelector(".name-select");
                        const datesWrap = row.querySelector(".dates-container");
                        show(datesWrap, hasValue(nameSelect));
                    }
                }
            });
        });
    </script>
@endpush
