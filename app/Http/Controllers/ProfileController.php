<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfilePersonalRequest;
use App\Http\Requests\UpdateProfilePersonalRequest;
use App\Http\Requests\SoldierServiceRequest;

use App\Models\Company;
use App\Models\District;
use App\Models\Rank;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Soldier;
use App\Models\SoldierServices;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index(): View
    {
        $query = Soldier::query();
        $profile = $query->paginate(10)->withQueryString();
        return view('mpm.page.profile.index', compact('profile'));
    }

    // <start>*************************Profile personal information<start>
    // <start>*************************Profile personal information<start>
    // <start>*************************Profile personal information<start>
    public function personalForm(?Soldier $profile = null): View
    {
        $district = District::all();
        $groupedRanks = Rank::where('status', true)->orderBy('type')->orderBy('id')->get()->groupBy('type');
        $company = Company::all();

        // Fetch profile either from route parameter or query string
        if (!$profile) {
            $profileId = request()->route('profile') ?? request()->query('id');
            if ($profileId) {
                $profile = Soldier::find($profileId);
                // dd($profile);
            }
        }

        $profileSteps = $this->getProfileSteps($profile);

        return view('mpm.page.profile.personal', compact('district', 'groupedRanks', 'company', 'profileSteps', 'profile'));
    }



    public function savePersonal(StoreProfilePersonalRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('profiles', 'public');
            }
            $data['personal_completed'] = true;
            $soldier = Soldier::create($data);
            // Redirect to next step with success message
            return redirect()->route('profile.serviceForm', $soldier->id)
                ->with('success', 'Personal information created successfully.');
        } catch (\Exception $e) {
            // Log the error for debugging (optional)
            \Log::error('Error saving personal profile: ' . $e->getMessage());

            // Redirect back with an error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while saving your profile. Please try again.');
        }
    }
    public function updatePersonal(UpdateProfilePersonalRequest $request, Soldier $soldier)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('image')) {
                if ($soldier->image && \Storage::disk('public')->exists($soldier->image)) {
                    \Storage::disk('public')->delete($soldier->image);
                }
                $data['image'] = $request->file('image')->store('profiles', 'public');
            }
            $soldier->update($data);
            // dd($data);
            return redirect()->back()
                ->with('success', 'Personal information updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating personal profile: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
    // <end>*************************Profile personal information<end>
    // <end>*************************Profile personal information<end>
    // <end>*************************Profile personal information<end>

    public function serviceForm($id)
    {
        // Eager load all services in a single query
        $profile = Soldier::with('services')->findOrFail($id);

        // Separate current and previous appointments from the loaded services
        $current = $profile->services->where('appointment_type', 'current')->last();
        $previous = $profile->services->where('appointment_type', 'previous');

        $profileSteps = $this->getProfileSteps($profile);

        return view('mpm.page.profile.service', compact('profileSteps', 'profile', 'current', 'previous'));
    }



    public function saveService(SoldierServiceRequest $request)
    {

        $profile = Soldier::findOrFail($request->id);

        DB::transaction(function () use ($request) {
            // === Delete old records first ===
            SoldierServices::where('soldier_id', $request->id)
                ->whereIn('appointment_type', ['previous', 'current'])
                ->delete();

            $insertData = [];

            // === Previous Appointments ===
            if ($request->has('previous_appointments')) {

                foreach ($request->previous_appointments as $prev) {
                    if (!empty($prev['name'])) {
                        $insertData[] = [
                            'appointments_name'      => $prev['name'],
                            'appointment_type'       => 'previous',
                            'soldier_id'             => $request->id,
                            'appointments_from_date' => $prev['from_date'] ?? null,
                            'appointments_to_date'   => $prev['to_date'] ?? null,
                        ];
                    }
                }
            }

            // === Current Appointments ===

            if ($request->filled('current_appointment_name')) {
                $insertData[] = [
                    'appointments_name' => $request->current_appointment_name,
                    'appointment_type' => 'current',
                    'soldier_id' => $request->id,
                    'appointments_from_date' => $request->current_appointment_from_date,
                    'appointments_to_date' => null,
                ];
            }

            // === Insert fresh data ===
            if (!empty($insertData)) {
                SoldierServices::insert($insertData);
            }
        });



        // Update soldier
        $profile->update([
            'service_completed' => 1,
            'joining_date' => $request->joining_date,
        ]);

        return redirect()->route('profile.qualificationsForm', $request->id);
    }

    public function qualificationsForm($id)
    {
        $profile = Soldier::findOrFail($id);

        $profileSteps = $this->getProfileSteps($profile);

        return view('mpm.page.profile.qualification', compact('profileSteps', 'profile'));
    }

    public function saveQualifications(Request $request)
    {
        $profile = Soldier::where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'degree' => 'required|string',
        ]);

        $profile->update($request->only(['degree']));

        return redirect()->route('profile.medicalForm');
    }

    public function medicalForm()
    {
        // $profile = Soldier::where('user_id', auth()->id())->first();

        // if (!$profile || !$profile->degree) {
        //     return redirect()->route('profile.qualificationsForm')
        //         ->with('error', 'Please complete qualifications first.');
        // }

        $profileSteps = $this->getProfileSteps();

        return view('mpm.page.profile.medical', compact('profileSteps'));
    }

    public function saveMedical(Request $request)
    {
        $profile = Soldier::where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'blood_group' => 'required|string',
            'allergies'   => 'nullable|string',
        ]);

        $profile->update($request->only(['blood_group', 'allergies']));

        return redirect()->route('profile.complete')
            ->with('success', 'Profile completed successfully!');
    }
    private function getProfileSteps($profile = null)
    {
        $profileId = $profile?->id;

        return [
            [
                'key'       => 'personal',
                'title'     => 'Personal',
                'routeName' => 'profile.personalForm',
                'params'    => $profileId ? ['id' => $profileId] : [],
                'icon'      => $profile?->personal_completed ? '✔' : '1',
                'enabled'   => true,
                'completed' => $profile?->personal_completed ?? false,
            ],
            [
                'key'       => 'service',
                'title'     => 'Service',
                'routeName' => $profileId && $profile?->personal_completed
                    ? 'profile.serviceForm'
                    : null,
                'params'    => $profileId && $profile?->personal_completed ? ['id' => $profileId] : [],
                'icon'      => $profile?->service_completed ? '✔' : '2',
                'enabled'   => $profile?->personal_completed ?? false,
                'completed' => $profile?->service_completed ?? false,
            ],
            [
                'key'       => 'qualifications',
                'title'     => 'Qualifications',
                'routeName' => $profileId && $profile?->service_completed
                    ? 'profile.qualificationsForm'
                    : null,
                'params'    => $profileId && $profile?->service_completed ? ['id' => $profileId] : [],

                'icon'      => $profile?->qualifications_completed ? '✔' : '3',

                'enabled'   => $profile?->service_completed ?? false,
                'completed' => $profile?->qualifications_completed ?? false,
            ],
            [
                'key'       => 'medical',
                'title'     => 'Medical',
                'routeName' => $profileId && $profile?->qualifications_completed
                    ? 'profile.medicalForm'
                    : null,
                'params'    => $profileId && $profile?->qualifications_completed ? ['id' => $profileId] : [],

                'icon'      => $profile?->medical_completed ? '✔' : '4',

                'enabled'   => $profile?->qualifications_completed ?? false,
                'completed' => $profile?->medical_completed ?? false,
            ],
        ];
    }
}
