@extends('mpm.layouts.app')

@section('title', 'Create Medical Information')

@section('content')
    <x-profile-step-nav :steps="$profileSteps" />

    <main class="container mx-auto p-6">
        <div class="grid md:grid-cols-12 gap-6 items-center">
            <div class="md:col-span-7 mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Medical & Disciplinary</h1>
                <p class="text-gray-500">Provide your medical status and any disciplinary records.</p>
            </div>
            <div class="md:col-span-5">
                @include('mpm.components.alerts')

            </div>
        </div>


        <div class="bg-white border rounded-lg p-8 shadow-sm">
            <form method="POST" action="{{ route('soldier.saveMedical', $profile->id) }}">
                @csrf
                <input type="hidden" name="action_update" value="{{ $profile->medical_completed ?? false }}" />
                {{-- Medical Section --}}
                <x-section-wrapper title="Medical Category" description="Add your current or past medical categories.">
                    @php
                        $medicalRows = old('medical', $soldierMedicalData ?? []);
                    @endphp

                    <div id="medical-container" class="space-y-4">
                        @forelse($medicalRows as $index => $medical)
                            <div
                                class="grid grid-cols-1 sm:grid-cols-4 gap-2 items-center border p-3 rounded-md relative form-row">
                                <x-form.select name="medical[{{ $index }}][category]" label="Category">
                                    <option value="">-- Select --</option>
                                    @foreach ($medicalCategory as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ ($medical['category'] ?? '') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </x-form.select>

                                <x-form.input name="medical[{{ $index }}][start_date]" type="date"
                                    label="Start Date" value="{{ $medical['start_date'] ?? '' }}" />
                                <x-form.input name="medical[{{ $index }}][end_date]" type="date" label="End Date"
                                    value="{{ $medical['end_date'] ?? '' }}" />
                                <x-form.input name="medical[{{ $index }}][remarks]" type="text" label="Remarks"
                                    value="{{ $medical['remarks'] ?? '' }}" />

                                <button type="button"
                                    class="absolute top-1 right-1 text-red-600 remove-row">&times;</button>
                            </div>
                        @empty
                            <div
                                class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center border p-3 rounded-md relative form-row">
                                <x-form.select name="medical[0][category]" label="Category">
                                    <option value="">-- Select --</option>
                                    @foreach ($medicalCategory as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </x-form.select>

                                <x-form.input name="medical[0][start_date]" type="date" label="Start Date" />
                                <x-form.input name="medical[0][end_date]" type="date" label="End Date" />
                                <x-form.input name="medical[0][remarks]" type="text" label="Remarks" />

                                <button type="button"
                                    class="absolute top-1 right-1 text-red-600 remove-row">&times;</button>
                            </div>
                        @endforelse
                        @if ($errors->has('medical'))
                            <span class="text-red-500">{{ $errors->first('medical') }}</span>
                        @endif

                        @if ($errors->has('medical.*.category'))
                            <span class="text-red-500">{{ $errors->first('medical.*.category') }}</span>
                        @endif
                    </div>
                    <button type="button" id="add-medical"
                        class="add-btn mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm">
                        + Add Category
                    </button>
                </x-section-wrapper>

                {{-- Sickness Section --}}
                <x-section-wrapper title="Permanent Sickness" description="List any permanent sickness if applicable.">
                    @php
                        $sicknessRows = old('sickness', $soldierSicknessData ?? []);
                    @endphp

                    <div id="sickness-container" class="space-y-4">
                        @forelse($sicknessRows as $index => $sick)
                            <div
                                class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center border p-3 rounded-md relative form-row">
                                <x-form.select name="sickness[{{ $index }}][category]" label="Sickness">
                                    <option value="">-- Select --</option>
                                    @foreach ($permanentSickness as $s)
                                        <option value="{{ $s->id }}"
                                            {{ ($sick['category'] ?? '') == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </x-form.select>

                                <x-form.input name="sickness[{{ $index }}][start_date]" type="date"
                                    label="Start Date" value="{{ $sick['start_date'] ?? '' }}" />
                                <x-form.input name="sickness[{{ $index }}][remarks]" type="text" label="Remarks"
                                    value="{{ $sick['remarks'] ?? '' }}" />

                                <button type="button"
                                    class="absolute top-1 right-1 text-red-600 remove-row">&times;</button>
                            </div>
                        @empty
                            <div
                                class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center border p-3 rounded-md relative form-row">
                                <x-form.select name="sickness[0][category]" label="Sickness">
                                    <option value="">-- Select --</option>
                                    @foreach ($permanentSickness as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </x-form.select>

                                <x-form.input name="sickness[0][start_date]" type="date" label="Start Date" />
                                <x-form.input name="sickness[0][remarks]" type="text" label="Remarks" />

                                <button type="button"
                                    class="absolute top-1 right-1 text-red-600 remove-row">&times;</button>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" id="add-sickness"
                        class="add-btn mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm">
                        + Add Sickness
                    </button>
                </x-section-wrapper>

                {{-- Punishments Section --}}
                <x-section-wrapper title="Disciplinary Records"
                    description="Mention any good behavior awards and list any punishments.">

                    @forelse($goodBehevior as $index => $good)
                        <x-form.input type="text" name="good_behavior" label="Good Behavior"
                            placeholder="e.g., Commendation for good service"
                            value="{{ old('good_behavior', $good['name'] ?? '') }}" />
                    @empty
                        <x-form.input type="text" name="good_behavior" label="Good Behavior"
                            placeholder="e.g., Commendation for good service" value="{{ old('good_behavior') }}" />
                    @endforelse


                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Punishments</label>

                        @php
                            $punishmentRows = old('punishments', $badBehavior ?? []);
                        @endphp

                        <div id="punishments-container" class="space-y-4">

                            @forelse($punishmentRows as $index => $punishment)
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center border p-3 rounded-md relative form-row">
                                    <x-form.input name="punishments[{{ $index }}][type]" label="Type"
                                        value="{{ $punishment['name'] ?? '' }}" />
                                    <x-form.input name="punishments[{{ $index }}][date]" type="date"
                                        label="Date" value="{{ $punishment['start_date'] ?? '' }}" />
                                    <x-form.input name="punishments[{{ $index }}][remarks]" label="Remarks"
                                        value="{{ $punishment['remarks'] ?? '' }}" />

                                    <button type="button"
                                        class="absolute top-1 right-1 text-red-600 remove-row">&times;</button>
                                </div>
                            @empty
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center border p-3 rounded-md relative form-row">
                                    <x-form.input name="punishments[0][type]" label="Type" />
                                    <x-form.input name="punishments[0][date]" type="date" label="Date" />
                                    <x-form.input name="punishments[0][remarks]" label="Remarks" />

                                    <button type="button"
                                        class="absolute top-1 right-1 text-red-600 remove-row">&times;</button>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="add-punishment"
                            class="add-btn mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm">
                            + Add Punishment
                        </button>
                    </div>
                </x-section-wrapper>

                {{-- Bottom Navigation --}}
                <div id="bottom-navigation" class="flex justify-between mt-6 border-t pt-6">
                    <button id="prev-btn"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded">
                        Previous
                    </button>
                    <button type="submit" id="next-btn"
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </main>

    {{-- Hidden Templates --}}
    <template id="medical-template">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center border p-3 rounded-md relative form-row">
            <x-form.select name="medical[__INDEX__][category]" label="Category">
                <option value="">-- Select --</option>
                @foreach ($medicalCategory as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </x-form.select>

            <x-form.input name="medical[__INDEX__][start_date]" type="date" label="Start Date" />
            <x-form.input name="medical[__INDEX__][end_date]" type="date" label="End Date" />
            <x-form.input name="medical[__INDEX__][remarks]" type="text" label="Remarks" />

            <button type="button" class="absolute top-1 right-1 text-red-600 remove-row">&times;</button>
        </div>
    </template>

    <template id="sickness-template">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center border p-3 rounded-md relative form-row">
            <x-form.select name="sickness[__INDEX__][category]" label="Sickness">
                <option value="">-- Select --</option>
                @foreach ($permanentSickness as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </x-form.select>

            <x-form.input name="sickness[__INDEX__][start_date]" type="date" label="Start Date" />
            <x-form.input name="sickness[__INDEX__][remarks]" type="text" label="Remarks" />

            <button type="button" class="absolute top-1 right-1 text-red-600 remove-row">&times;</button>
        </div>
    </template>

    <template id="punishment-template">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center border p-3 rounded-md relative form-row">
            <x-form.input name="punishments[__INDEX__][type]" label="Type" />
            <x-form.input name="punishments[__INDEX__][date]" type="date" label="Date" />
            <x-form.input name="punishments[__INDEX__][remarks]" label="Remarks" />

            <button type="button" class="absolute top-1 right-1 text-red-600 remove-row">&times;</button>
        </div>
    </template>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addRow = (containerId, templateId) => {
                const container = document.getElementById(containerId);
                const index = container.querySelectorAll('.form-row').length;
                const template = document.getElementById(templateId);
                let html = template.innerHTML.replace(/__INDEX__/g, index);
                container.insertAdjacentHTML('beforeend', html);
            }

            // Add button handlers
            document.getElementById('add-medical')?.addEventListener('click', () => addRow('medical-container',
                'medical-template'));
            document.getElementById('add-sickness')?.addEventListener('click', () => addRow('sickness-container',
                'sickness-template'));
            document.getElementById('add-punishment')?.addEventListener('click', () => addRow(
                'punishments-container',
                'punishment-template'));

            // Remove row handler
            document.addEventListener('click', e => {
                if (e.target.classList.contains('remove-row')) {
                    e.target.closest('.form-row').remove();
                }
            });
        });
    </script>
@endpush
