<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\District;
use App\Models\Rank;
use Illuminate\Http\Request;
use App\Models\Soldier;

class ProfileController extends Controller
{
    public function index()
    {
        $query = Soldier::query();
        $profile = $query->paginate(10)->withQueryString();
        return view('mpm.page.profile.index', compact('profile'));
    }
    public function personalForm()
    {
        $district = District::get();
        $ranks = Rank::where('status', true)->orderBy('type')->orderBy('id')->get();
        $groupedRanks = $ranks->groupBy('type');
        $company = Company::get();

        $profileSteps = $this->getProfileSteps();

        return view('mpm.page.profile.personal', compact('district', 'groupedRanks', 'company', 'profileSteps'));
    }


    public function savePersonal(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'dob'  => 'required|date',
        ]);

        $profile = Soldier::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->only(['name', 'dob'])
        );

        // redirect to next step
        return redirect()->route('profile.serviceForm');
    }

    public function serviceForm()
    {
        // $profile = Soldier::where('user_id', auth()->id())->first();

        // // prevent skipping step
        // if (!$profile || !$profile->name || !$profile->dob) {
        //     return redirect()->route('profile.personalForm')
        //         ->with('error', 'Please complete personal info first.');
        // }

        $profileSteps = $this->getProfileSteps();

        return view('mpm.page.profile.service', compact('profileSteps'));
    }

    public function saveService(Request $request)
    {
        $profile = Soldier::where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'service_type' => 'required|string',
            'joining_date' => 'required|date',
        ]);

        $profile->update($request->only(['service_type', 'joining_date']));

        return redirect()->route('profile.qualificationsForm');
    }

    public function qualificationsForm()
    {
        // $profile = Soldier::where('user_id', auth()->id())->first();

        // if (!$profile || !$profile->service_type) {
        //     return redirect()->route('profile.serviceForm')
        //         ->with('error', 'Please complete service info first.');
        // }
        $profileSteps = $this->getProfileSteps();

        return view('mpm.page.profile.qualification', compact('profileSteps'));
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
    private function getProfileSteps()
    {
        return [
            ['key' => 'personal', 'title' => 'Personal', 'route' => route('profile.personalForm'), 'icon' => '1'],
            ['key' => 'service', 'title' => 'Service', 'route' => route('profile.serviceForm'), 'icon' => '2'],
            ['key' => 'qualifications', 'title' => 'Qualifications', 'route' => route('profile.qualificationsForm'), 'icon' => '3'],
            ['key' => 'medical', 'title' => 'Medical', 'route' => route('profile.medicalForm'), 'icon' => '4'],
        ];
    }
}
