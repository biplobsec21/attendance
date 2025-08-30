@extends('mpm.layouts.app')

@section('title', 'Create Qualification')

@section('content')
    {{-- Profile Steps Navigation --}}

    <x-profile-step-nav :steps="$profileSteps" />


    <main class="container mx-auto p-6 ">
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-800">Qualifications & Activities</h1>
            <p class="text-gray-500">List your academic background, courses, and other professional qualifications.
            </p>
        </div>
        <div class="bg-white border rounded-lg p-8">
            <form>
                <div class="grid grid-cols-12 gap-8 pb-8">
                    <div class="col-span-12 md:col-span-4"><label class="font-bold text-gray-700">Education</label>
                        <p class="text-sm text-gray-500 mt-1">Add your academic qualifications.</p>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        <div id="education-container" class="space-y-4"></div><button type="button" id="add-education"
                            class="add-btn mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm">+
                            Add Education</button>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-8 border-t pt-8 pb-8">
                    <div class="col-span-12 md:col-span-4"><label class="font-bold text-gray-700">Courses</label>
                        <p class="text-sm text-gray-500 mt-1">List any professional courses.</p>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        <div id="courses-container" class="space-y-4"></div><button type="button" id="add-course"
                            class="add-btn mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm">+
                            Add Course</button>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-8 border-t pt-8 pb-8">
                    <div class="col-span-12 md:col-span-4"><label class="font-bold text-gray-700">Cadres</label>
                        <p class="text-sm text-gray-500 mt-1">List any professional cadres.</p>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        <div id="cadres-container" class="space-y-4"></div><button type="button" id="add-cadre"
                            class="add-btn mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm">+
                            Add Cadre</button>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-8 border-t pt-8 pb-8">
                    <div class="col-span-12 md:col-span-4"><label class="font-bold text-gray-700">Co-Curricular
                            Activities</label>
                        <p class="text-sm text-gray-500 mt-1">Include sports, clubs, etc.</p>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        <div id="cocurricular-container" class="space-y-4"></div><button type="button"
                            id="add-cocurricular"
                            class="add-btn mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm">+
                            Add Activity</button>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-8 border-t pt-8 pb-8">
                    <div class="col-span-12 md:col-span-4"><label class="font-bold text-gray-700">ERE</label>
                        <p class="text-sm text-gray-500 mt-1">List your ERE history.</p>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        <div id="ere-container" class="space-y-4"></div><button type="button" id="add-ere"
                            class="add-btn mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm">+
                            Add ERE</button>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-8 border-t pt-8">
                    <div class="col-span-12 md:col-span-4"><label class="font-bold text-gray-700">Att</label>
                        <p class="text-sm text-gray-500 mt-1">List any attachments.</p>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        <div id="att-container" class="space-y-4"></div><button type="button" id="add-att"
                            class="add-btn mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm">+
                            Add Attachment</button>
                    </div>
                </div>
                <div id="bottom-navigation" class="flex justify-between mt-6 border-t pt-6">
                    <button id="prev-btn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded"
                        style="display: inline-flex;">Previous</button>
                    <button id="next-btn"
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded">Save
                        &amp; Continue</button>
                </div>
            </form>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- QUALIFICATIONS.HTML & MEDICAL.HTML COMBINED SCRIPT ---

            // --- Pre-populated Data ---
            const educationNames = ["Select an Education", "Primary School Certificate (PSC)",
                "Junior School Certificate (JSC)", "Secondary School Certificate (SSC)",
                "Higher Secondary Certificate (HSC)", "Diploma", "Bachelor of Arts (BA)",
                "Bachelor of Science (BSc)", "Bachelor of Commerce (BCom)",
                "Bachelor of Business Administration (BBA)", "Master of Arts (MA)", "Master of Science (MSc)",
                "Master of Commerce (MCom)", "Master of Business Administration (MBA)",
                "Doctor of Philosophy (PhD)"
            ];
            const armyCourses = ["Select a Course", "Basic Military Training", "Officer Basic Course (OBC)",
                "Junior Command and Staff Course (JCSC)", "Army Staff Course (ASC)",
                "National Defence Course (NDC)", "War Course", "Weapon Handling Course",
                "Physical Training (PT) Course", "Drill Course", "Signals Course", "Engineers Course",
                "Artillery Course", "Infantry Course", "Counter Terrorism Course"
            ];
            const armyCadres = ["Select a Cadre", "Infantry", "Artillery", "Armoured Corps", "Engineers", "Signals",
                "Army Service Corps (ASC)", "Army Medical Corps (AMC)", "Ordnance Corps",
                "Corps of Military Police (CMP)", "Army Education Corps (AEC)",
                "Remount Veterinary and Farm Corps (RVFC)", "Dental Corps", "East Bengal Regiment",
                "Bangladesh Infantry Regiment"
            ];
            const armyCocurricular = ["Select an Activity", "Football", "Hockey", "Volleyball", "Handball",
                "Basketball", "Swimming", "Boxing", "Shooting", "Athletics", "Debate Club", "Photography Club"
            ];
            const armyEre = ["Select an ERE", "Instructor - BMA", "Instructor - SI&T", "Instructor - Other School",
                "Staff - AHQ", "Staff - Division HQ", "Staff - Brigade HQ", "UN Mission", "Aide-de-Camp (ADC)",
                "MS to President/PM"
            ];
            const armyAtt = ["Select an Attachment", "Attachment with BMA", "Attachment with other unit",
                "Attachment with DGFI", "Attachment with NSI", "Attachment with Civil Org."
            ];
            const medicalCategories = ["Select a Category", "Category A (Fit for all duties)",
                "Category B (Temporarily unfit)", "Category C (Permanently lowered)",
                "Category E (Unfit for service)"
            ];
            const permanentSicknesses = ["Select a Sickness", "None", "Hypertension", "Diabetes", "Asthma",
                "Chronic Back Pain", "Hearing Loss", "Vision Impairment"
            ];

            // --- Modal Logic ---
            const mainContainer = document.querySelector('main');
            const modal = document.getElementById('add-item-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalInput = document.getElementById('modal-input');
            const modalSaveBtn = document.getElementById('modal-save');
            const modalCloseBtn = document.getElementById('modal-close');
            let activeSelectElement = null;


            // --- Dynamic Row Creation & Population ---
            function createDynamicRow(config) {
                const div = document.createElement('div');
                div.className = config.gridClass;
                div.innerHTML = config.innerHTML;
                if (config.data) {
                    const select = div.querySelector('.name-select');
                    config.data.forEach(item => select.add(new Option(item, item)));
                }
                return div;
            }

            const qualificationsRowConfigs = {
                'education': {
                    container: 'education-container',
                    dataType: 'Qualification',
                    data: educationNames,
                    gridClass: 'grid grid-cols-1 sm:grid-cols-10 gap-2 items-center border p-3 rounded-md',
                    innerHTML: `<div class="flex items-center sm:col-span-4">
                                    <select class="w-full p-2 border-r-0 border rounded-l-md bg-white name-select"></select>
                                    <button type="button" class="add-new-item-btn bg-orange-500 text-white p-2 rounded-r-md hover:bg-orange-600" data-type="Qualification">+</button>
                                </div>
                                <div class="status-container hidden sm:col-span-3">
                                    <select class="status-select w-full p-2 border rounded-md bg-white">
                                        <option value="">Select Status</option>
                                        <option value="Running">Running Student</option>
                                        <option value="Passed">Passed</option>
                                    </select>
                                </div>
                                <div class="year-container hidden sm:col-span-2">
                                    <input type="text" placeholder="Pass Year" class="w-full p-2 border rounded-md">
                                </div>
                                <div class="sm:col-span-1 flex justify-end">
                                    <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full">✕</button>
                                </div>`
                },
                'course': {
                    container: 'courses-container',
                    dataType: 'Course',
                    data: armyCourses,
                    gridClass: 'grid grid-cols-1 md:grid-cols-12 gap-2 items-center border p-3 rounded-md',
                    innerHTML: `<div class="flex items-center md:col-span-4">
                                    <select class="w-full p-2 border-r-0 border rounded-l-md bg-white name-select"></select>
                                    <button type="button" class="add-new-item-btn bg-orange-500 text-white p-2 rounded-r-md hover:bg-orange-600" data-type="Course">+</button>
                                </div>
                                <div class="status-container hidden md:col-span-2">
                                    <select class="status-select w-full p-2 border rounded-md bg-white">
                                        <option value="">Select Status</option>
                                        <option value="Running">Running</option>
                                        <option value="Passed">Passed</option>
                                    </select>
                                </div>
                                <div class="passed-fields-container hidden md:col-span-5 grid grid-cols-3 gap-2">
                                    <input type="date" title="Start Date" class="w-full p-2 border rounded-md">
                                    <input type="date" title="End Date" class="w-full p-2 border rounded-md">
                                    <input type="text" placeholder="Result" class="w-full p-2 border rounded-md">
                                </div>
                                <div class="md:col-span-1 flex justify-end">
                                    <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full">✕</button>
                                </div>`
                },
                'cadre': {
                    container: 'cadres-container',
                    dataType: 'Cadre',
                    data: armyCadres,
                    gridClass: 'grid grid-cols-1 md:grid-cols-12 gap-2 items-center border p-3 rounded-md',
                    innerHTML: `<div class="flex items-center md:col-span-4">
                                    <select class="w-full p-2 border-r-0 border rounded-l-md bg-white name-select"></select>
                                    <button type="button" class="add-new-item-btn bg-orange-500 text-white p-2 rounded-r-md hover:bg-orange-600" data-type="Cadre">+</button>
                                </div>
                                <div class="status-container hidden md:col-span-2">
                                    <select class="status-select w-full p-2 border rounded-md bg-white">
                                        <option value="">Select Status</option>
                                        <option value="Running">Running</option>
                                        <option value="Passed">Passed</option>
                                    </select>
                                </div>
                                <div class="passed-fields-container hidden md:col-span-5 grid grid-cols-3 gap-2">
                                    <input type="date" title="Start Date" class="w-full p-2 border rounded-md">
                                    <input type="date" title="End Date" class="w-full p-2 border rounded-md">
                                    <input type="text" placeholder="Result" class="w-full p-2 border rounded-md">
                                </div>
                                <div class="md:col-span-1 flex justify-end">
                                    <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full">✕</button>
                                </div>`
                },
                'cocurricular': {
                    container: 'cocurricular-container',
                    dataType: 'Activity',
                    data: armyCocurricular,
                    gridClass: 'grid grid-cols-1 sm:grid-cols-4 gap-2 items-center border p-3 rounded-md',
                    innerHTML: `<div class="flex items-center sm:col-span-2">
                                    <select class="w-full p-2 border-r-0 border rounded-l-md bg-white name-select"></select>
                                    <button type="button" class="add-new-item-btn bg-orange-500 text-white p-2 rounded-r-md hover:bg-orange-600" data-type="Activity">+</button>
                                </div>
                                <div class="remark-container hidden sm:col-span-1">
                                    <input type="text" placeholder="Achievement / Remark" class="w-full p-2 border rounded-md">
                                </div>
                                <div class="sm:col-span-1 flex justify-end">
                                    <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full">✕</button>
                                </div>`
                },
                'ere': {
                    container: 'ere-container',
                    dataType: 'ERE',
                    data: armyEre,
                    gridClass: 'grid grid-cols-1 sm:grid-cols-5 gap-2 items-center border p-3 rounded-md',
                    innerHTML: `<div class="flex items-center sm:col-span-2">
                                    <select class="w-full p-2 border-r-0 border rounded-l-md bg-white name-select"></select>
                                    <button type="button" class="add-new-item-btn bg-orange-500 text-white p-2 rounded-r-md hover:bg-orange-600" data-type="ERE">+</button>
                                </div>
                                <div class="dates-container hidden sm:col-span-2 grid grid-cols-2 gap-2">
                                    <input type="date" title="Start Date" class="w-full p-2 border rounded-md">
                                    <input type="date" title="End Date" class="w-full p-2 border rounded-md">
                                </div>
                                <div class="sm:col-span-1 flex justify-end">
                                    <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full">✕</button>
                                </div>`
                },
                'att': {
                    container: 'att-container',
                    dataType: 'Attachment',
                    data: armyAtt,
                    gridClass: 'grid grid-cols-1 sm:grid-cols-5 gap-2 items-center border p-3 rounded-md',
                    innerHTML: `<div class="flex items-center sm:col-span-2">
                                    <select class="w-full p-2 border-r-0 border rounded-l-md bg-white name-select"></select>
                                    <button type="button" class="add-new-item-btn bg-orange-500 text-white p-2 rounded-r-md hover:bg-orange-600" data-type="Attachment">+</button>
                                </div>
                                <div class="dates-container hidden sm:col-span-2 grid grid-cols-2 gap-2">
                                    <input type="date" title="Start Date" class="w-full p-2 border rounded-md">
                                    <input type="date" title="End Date" class="w-full p-2 border rounded-md">
                                </div>
                                <div class="sm:col-span-1 flex justify-end">
                                    <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full">✕</button>
                                </div>`
                }
            };

            const medicalRowConfigs = {
                'medical': {
                    container: 'medical-container',
                    dataType: 'Medical Category',
                    data: medicalCategories,
                    gridClass: 'grid grid-cols-1 sm:grid-cols-6 gap-2 items-center border p-3 rounded-md',
                    innerHTML: `<div class="flex items-center sm:col-span-2">
                                    <select class="w-full p-2 border-r-0 border rounded-l-md bg-white name-select"></select>
                                    <button type="button" class="add-new-item-btn bg-orange-500 text-white p-2 rounded-r-md hover:bg-orange-600" data-type="Medical Category">+</button>
                                </div>
                                <div class="details-container hidden sm:col-span-3 grid grid-cols-3 gap-2">
                                    <input type="date" title="Start Date" class="w-full p-2 border rounded-md">
                                    <input type="date" title="End Date" class="w-full p-2 border rounded-md">
                                    <input type="text" placeholder="Remarks" class="w-full p-2 border rounded-md">
                                </div>
                                <div class="sm:col-span-1 flex justify-end">
                                    <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full">✕</button>
                                </div>`
                },
                'sickness': {
                    container: 'sickness-container',
                    dataType: 'Sickness',
                    data: permanentSicknesses,
                    gridClass: 'grid grid-cols-1 sm:grid-cols-5 gap-2 items-center border p-3 rounded-md',
                    innerHTML: `<div class="flex items-center sm:col-span-2">
                                    <select class="w-full p-2 border-r-0 border rounded-l-md bg-white name-select"></select>
                                    <button type="button" class="add-new-item-btn bg-orange-500 text-white p-2 rounded-r-md hover:bg-orange-600" data-type="Sickness">+</button>
                                </div>
                                <div class="details-container hidden sm:col-span-2 grid grid-cols-2 gap-2">
                                    <input type="date" title="Start Date" class="w-full p-2 border rounded-md">
                                    <input type="text" placeholder="Remarks" class="w-full p-2 border rounded-md">
                                </div>
                                <div class="sm:col-span-1 flex justify-end">
                                    <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full">✕</button>
                                </div>`
                },
                'punishment': {
                    container: 'punishments-container',
                    gridClass: 'grid grid-cols-1 sm:grid-cols-4 gap-2 items-center border p-3 rounded-md',
                    innerHTML: `<input type="text" placeholder="Punishment Name" class="sm:col-span-2 w-full p-2 border rounded-md"><input type="text" placeholder="Detail" class="w-full p-2 border rounded-md"><button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full">✕</button>`
                }
            };

            const allRowConfigs = {
                ...qualificationsRowConfigs,
                ...medicalRowConfigs
            };
            for (const key in allRowConfigs) {
                const addButton = document.getElementById(`add-${key}`);
                if (addButton) {
                    addButton.addEventListener('click', () => {
                        const config = allRowConfigs[key];
                        document.getElementById(config.container).appendChild(createDynamicRow(config));
                    });
                }
            }

            // --- Generic Event Delegation for main container ---
            mainContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-row')) {
                    const parentGrid = e.target.closest('.grid');
                    if (parentGrid) {
                        parentGrid.remove();
                    }
                }
                if (e.target.classList.contains('add-new-item-btn')) {
                    const select = e.target.previousElementSibling;
                    const type = e.target.dataset.type;
                    openModal(`Add New ${type}`, select);
                }
            });

            mainContainer.addEventListener('change', (e) => {
                // Check if the changed element is a select within a dynamic row
                if (e.target.matches('.name-select, .status-select')) {
                    const parentRow = e.target.closest('.grid');
                    if (!parentRow) return;

                    const parentId = parentRow.parentElement.id;

                    // Handle Education row logic
                    if (parentId === 'education-container') {
                        const nameSelect = parentRow.querySelector('.name-select');
                        const statusContainer = parentRow.querySelector('.status-container');
                        const statusSelect = parentRow.querySelector('.status-select');
                        const yearContainer = parentRow.querySelector('.year-container');

                        const isNameSelected = nameSelect.value && nameSelect.value !== educationNames[0];
                        statusContainer.classList.toggle('hidden', !isNameSelected);

                        const isStatusPassed = statusSelect.value === 'Passed';
                        yearContainer.classList.toggle('hidden', !isStatusPassed);

                        if (!isNameSelected) {
                            yearContainer.classList.add('hidden');
                        }
                    }
                    // Handle Course row logic
                    else if (parentId === 'courses-container') {
                        const nameSelect = parentRow.querySelector('.name-select');
                        const statusContainer = parentRow.querySelector('.status-container');
                        const statusSelect = parentRow.querySelector('.status-select');
                        const passedFields = parentRow.querySelector('.passed-fields-container');

                        const isNameSelected = nameSelect.value && nameSelect.value !== armyCourses[0];
                        statusContainer.classList.toggle('hidden', !isNameSelected);

                        const isStatusPassed = statusSelect.value === 'Passed';
                        passedFields.classList.toggle('hidden', !isStatusPassed);

                        if (!isNameSelected) {
                            passedFields.classList.add('hidden');
                        }
                    }
                    // Handle Cadre row logic
                    else if (parentId === 'cadres-container') {
                        const nameSelect = parentRow.querySelector('.name-select');
                        const statusContainer = parentRow.querySelector('.status-container');
                        const statusSelect = parentRow.querySelector('.status-select');
                        const passedFields = parentRow.querySelector('.passed-fields-container');

                        const isNameSelected = nameSelect.value && nameSelect.value !== armyCadres[0];
                        statusContainer.classList.toggle('hidden', !isNameSelected);

                        const isStatusPassed = statusSelect.value === 'Passed';
                        passedFields.classList.toggle('hidden', !isStatusPassed);

                        if (!isNameSelected) {
                            passedFields.classList.add('hidden');
                        }
                    }
                    // Handle Co-Curricular row logic
                    else if (parentId === 'cocurricular-container') {
                        const nameSelect = parentRow.querySelector('.name-select');
                        const remarkContainer = parentRow.querySelector('.remark-container');

                        const isNameSelected = nameSelect.value && nameSelect.value !== armyCocurricular[0];
                        remarkContainer.classList.toggle('hidden', !isNameSelected);
                    }
                    // Handle ERE row logic
                    else if (parentId === 'ere-container') {
                        const nameSelect = parentRow.querySelector('.name-select');
                        const datesContainer = parentRow.querySelector('.dates-container');

                        const isNameSelected = nameSelect.value && nameSelect.value !== armyEre[0];
                        datesContainer.classList.toggle('hidden', !isNameSelected);
                    }
                    // Handle ATT row logic
                    else if (parentId === 'att-container') {
                        const nameSelect = parentRow.querySelector('.name-select');
                        const datesContainer = parentRow.querySelector('.dates-container');

                        const isNameSelected = nameSelect.value && nameSelect.value !== armyAtt[0];
                        datesContainer.classList.toggle('hidden', !isNameSelected);
                    }
                    // Handle Medical Category row logic
                    else if (parentId === 'medical-container') {
                        const nameSelect = parentRow.querySelector('.name-select');
                        const detailsContainer = parentRow.querySelector('.details-container');

                        const isNameSelected = nameSelect.value && nameSelect.value !== medicalCategories[
                            0];
                        detailsContainer.classList.toggle('hidden', !isNameSelected);
                    }
                    // Handle Permanent Sickness row logic
                    else if (parentId === 'sickness-container') {
                        const nameSelect = parentRow.querySelector('.name-select');
                        const detailsContainer = parentRow.querySelector('.details-container');

                        const isNameSelected = nameSelect.value && nameSelect.value !== permanentSicknesses[
                            0];
                        detailsContainer.classList.toggle('hidden', !isNameSelected);
                    }
                }
            });

        });
    </script>
@endpush
