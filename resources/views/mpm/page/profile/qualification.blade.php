@extends('mpm.layouts.app')

@section('title', 'Create Qualification')

@section('content')
    <x-profile-step-nav :steps="$profileSteps" />
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <main class="container mx-auto p-6">

        <div class="grid md:grid-cols-12 gap-6 items-center">
            <div class="md:col-span-7 mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Qualifications & Activities</h1>
                <p class="text-gray-500">Add your academic background, courses, and other professional qualifications.</p>

            </div>
            <div class="md:col-span-5">
                @include('mpm.components.alerts')
            </div>
        </div>


        <div class="bg-white border rounded-2xl p-8 shadow-lg">
            <form action="{{ route('profile.saveQualifications', $profile->id) }}" method="post">
                @csrf
                <input type="hidden" name="action_update" value="{{ $profile->qualifications_completed ?? false }}" />
                @foreach ($sections as $key => $section)
                    @php
                        $isEven = $loop->index % 2 === 0;
                        $rowBg = $isEven ? 'bg-orange-50' : 'bg-blue-50';
                        $btnBg = $isEven
                            ? 'bg-orange-100 hover:bg-orange-200 text-orange-600'
                            : 'bg-blue-100 hover:bg-blue-200 text-blue-600';
                    @endphp

                    <div class="mb-8 p-4 rounded-lg shadow-sm {{ $rowBg }}">
                        <div class="flex justify-between items-center mb-4">
                            {{-- Modern Label --}}
                            <div>
                                <span
                                    class="inline-block px-3 py-1 font-semibold text-white text-lg rounded-full
                             bg-gradient-to-r {{ $isEven ? 'from-orange-400 to-orange-500' : 'from-blue-400 to-blue-500' }}
                             shadow-md">
                                    {{ $section['label'] }}
                                </span>
                                <p class="text-gray-600 mt-2 text-sm">{{ $section['description'] }}</p>
                            </div>

                            {{-- Add Button --}}
                            <button type="button" id="add-{{ $key }}"
                                class="add-btn flex items-center gap-1 {{ $btnBg }} font-semibold py-2 px-4 rounded-lg text-sm transition shadow-sm hover:scale-105">
                                <span class="text-xl font-bold">+</span> Add
                            </button>
                        </div>

                        <div id="{{ $key }}-container" class="space-y-4 grid grid-cols-2 gap-4">
                            {{-- Dynamic rows will be appended here --}}
                        </div>
                    </div>
                @endforeach
                <div class="flex justify-between mt-6 border-t pt-6">
                    <button type="button" id="prev-btn"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">
                        ← Previous
                    </button>
                    <button type="submit"
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg transition">
                        Save & Continue
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const sections = @json($sections);

            const existingData = {
                education: @json($educationsData ?? []),
                courses: @json($coursesData ?? []),
                cadres: @json($cadresData ?? []),
                cocurricular: @json($cocurricular ?? []),
                attachments: @json($attData ?? []),
                ere: @json($ereData ?? [])
            };

            console.log(existingData);
            // Merge old() and existing DB data: old() has priority
            const oldData = {};
            Object.keys(existingData).forEach(key => {
                const oldSection = @json(old())?.[key] ?? null;
                if (oldSection && oldSection.length) {
                    oldData[key] = oldSection; // use old input if exists
                } else if (existingData[key] && existingData[key].length) {
                    oldData[key] = existingData[key]; // fallback to DB values
                } else {
                    oldData[key] = [{}]; // empty row if nothing exists
                }
            });
            const errors = @json($errors->getMessages());

            function createField(type, name, value, error, options = null, placeholder = '') {
                const fieldDiv = document.createElement('div');
                fieldDiv.className = 'flex-1 relative';

                let inputEl;
                if (options) {
                    inputEl = document.createElement('select');
                    inputEl.name = name;
                    inputEl.innerHTML = `<option value="">Select ${placeholder}</option>` +
                        options.map(opt => typeof opt === 'object' ?
                            `<option value="${opt.id}" ${value==opt.id?'selected':''}>${opt.name}</option>` :
                            `<option value="${opt}" ${value==opt?'selected':''}>${opt}</option>`
                        ).join('');
                    inputEl.className =
                        'w-full p-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none transition';
                } else {
                    inputEl = document.createElement('input');
                    inputEl.type = type;
                    inputEl.placeholder = placeholder;
                    inputEl.name = name;
                    inputEl.value = value || '';
                    inputEl.className =
                        'w-full p-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none transition';
                }

                fieldDiv.appendChild(inputEl);

                if (error) {
                    const errDiv = document.createElement('div');
                    errDiv.className = 'text-red-600 text-sm mt-1';
                    errDiv.textContent = error;
                    inputEl.classList.add('border-red-500');
                    fieldDiv.appendChild(errDiv);
                }

                return fieldDiv;
            }

            function createRow(section, index = 0, oldRow = {}) {
                const row = document.createElement('div');
                row.setAttribute('role', 'qualification-row');
                row.className =
                    'flex flex-wrap items-start gap-2 p-4 border rounded-xl bg-gray-50 relative hover:shadow-md transition';
                row.dataset.type = section.type;

                // Remove button
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className =
                    'remove-row absolute -top-3 -right-3 text-white bg-red-500 hover:bg-red-600 rounded-full w-6 h-6 flex items-center justify-center text-sm transition';
                removeBtn.textContent = '×';
                row.appendChild(removeBtn);

                // Name field
                const mainWrapper = document.createElement('div');
                mainWrapper.className = 'flex items-center gap-2 w-full';
                const nameField = createField(
                    'text',
                    `${section.type}[${index}][name]`,
                    oldRow.name || '',
                    errors[`${section.type}.${index}.name`] ? errors[`${section.type}.${index}.name`][0] : null,
                    section.options,
                    section.label
                );
                const nameSelect = nameField.querySelector('select');
                if (nameSelect) nameSelect.classList.add('name-select', 'flex-1');
                mainWrapper.appendChild(nameField);
                row.appendChild(mainWrapper);

                // Conditional fields container
                const conditionalWrapper = document.createElement('div');
                conditionalWrapper.className = 'flex flex-wrap gap-4 mt-3 w-full';

                // Status
                if ('status' in section.fields) {
                    const fieldVal = oldRow.status || '';
                    const fieldError = errors[`${section.type}.${index}.status`] ? errors[
                        `${section.type}.${index}.status`][0] : null;
                    const statusDiv = createField('text', `${section.type}[${index}][status]`, fieldVal, fieldError,
                        section.fields.status, 'Status');
                    statusDiv.classList.add('status-container', 'flex-1');
                    conditionalWrapper.appendChild(statusDiv);
                }

                // Education Year field
                if (section.type === 'education' && 'year' in section.fields) {
                    const yearDiv = createField(
                        'text',
                        `${section.type}[${index}][year]`,
                        oldRow.year || '',
                        errors[`${section.type}.${index}.year`] ? errors[`${section.type}.${index}.year`][0] :
                        null,
                        null,
                        section.fields.year
                    );
                    yearDiv.classList.add('year-container', 'w-full');
                    conditionalWrapper.appendChild(yearDiv);
                }

                // Start/End Dates aligned horizontally
                if ('start_date' in section.fields && 'end_date' in section.fields) {
                    const dateDiv = document.createElement('div');
                    dateDiv.className = 'flex gap-2 dates-container w-full';
                    ['start_date', 'end_date'].forEach(d => {
                        const input = createField(
                            'date',
                            `${section.type}[${index}][${d}]`,
                            oldRow[d] || '',
                            errors[`${section.type}.${index}.${d}`] ? errors[
                                `${section.type}.${index}.${d}`][0] : null
                        );
                        input.querySelector('input')?.classList.add('w-full');
                        dateDiv.appendChild(input);
                    });
                    conditionalWrapper.appendChild(dateDiv);
                }

                // Result/Remark field hidden by default
                if (section.fields && ('result' in section.fields || 'remark' in section.fields) && section.type !==
                    'education') {
                    const remarkKey = 'result' in section.fields ? 'result' : 'remark';
                    const remarkDiv = createField(
                        'text',
                        `${section.type}[${index}][${remarkKey}]`,
                        oldRow[remarkKey] || '',
                        errors[`${section.type}.${index}.${remarkKey}`] ? errors[
                            `${section.type}.${index}.${remarkKey}`][0] : null,
                        null,
                        section.fields[remarkKey]
                    );
                    remarkDiv.classList.add('remark-container', 'w-full');
                    conditionalWrapper.appendChild(remarkDiv);
                }

                row.appendChild(conditionalWrapper);
                return row;
            }

            // Render old rows
            Object.keys(sections).forEach(key => {
                const container = document.getElementById(`${key}-container`);
                const oldRows = oldData[key] || [{}];
                oldRows.forEach((rowData, idx) => container.appendChild(createRow({
                    ...sections[key],
                    type: key
                }, idx, rowData)));
            });

            // Add new row
            document.querySelectorAll('.add-btn').forEach(btn => btn.addEventListener('click', () => {
                const type = btn.id.replace('add-', '');
                const container = document.getElementById(`${type}-container`);
                const index = container.children.length;
                container.appendChild(createRow({
                    ...sections[type],
                    type
                }, index));
            }));

            // Remove row
            document.addEventListener('click', e => {
                const rm = e.target.closest('.remove-row');
                if (rm) rm.parentElement.remove();
            });

            // Toggle conditional fields
            const toggleConditional = (row) => {
                const nameSelect = row.querySelector('.name-select');
                const statusSelect = row.querySelector('.status-container select, .status-container input');
                if (!nameSelect || !statusSelect) return;

                const sectionType = row.dataset.type;

                // Status container always visible if name selected
                const statusContainer = row.querySelector('.status-container');
                if (statusContainer) statusContainer.classList.toggle('hidden', !nameSelect.value);

                const passed = statusSelect.value === 'Passed';

                // Education year
                if (sectionType === 'education') {
                    const yearContainer = row.querySelector('.year-container');
                    if (yearContainer) yearContainer.classList.toggle('hidden', !passed);
                }

                // Start/End Dates
                const datesContainer = row.querySelector('.dates-container');
                if (datesContainer) datesContainer.classList.toggle('hidden', !passed);

                // Result/Remark for other sections
                const remarkContainer = row.querySelector('.remark-container');
                const hasRemarkField = sections[sectionType] && sections[sectionType].fields &&
                    ('remark' in sections[sectionType].fields || 'result' in sections[sectionType].fields);
                if (remarkContainer) remarkContainer.classList.toggle('hidden', !passed || !hasRemarkField);
            };

            // On change toggle
            document.addEventListener('change', e => {
                const row = e.target.closest('div[role="qualification-row"]');
                if (row) toggleConditional(row);
            });

            // Initial toggle for old rows
            document.querySelectorAll('div[role="qualification-row"]').forEach(row => toggleConditional(row));
        });
    </script>
@endpush
