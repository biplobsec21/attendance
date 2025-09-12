<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfilePersonalRequest;
use App\Http\Requests\UpdateProfilePersonalRequest;
use App\Http\Requests\SoldierServiceRequest;
use App\Http\Requests\StoreMedicalRequest;
use App\Http\Requests\StoreQualificationRequest;
use App\Models\Atts;
use App\Models\Cadre;
use App\Models\Company;
use App\Models\Course;
use App\Models\District;
use App\Models\Education;
use App\Models\Eres;
use App\Models\MedicalCategory;
use App\Models\PermanentSickness;
use App\Models\Rank;
use App\Models\Service;
use App\Models\Skill;
use Illuminate\Http\Request;
use App\Models\Soldier;
use App\Models\SoldierServices;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Services\SoldierDataFormatter;

class SoldierController extends Controller
{
    protected $formatter;

    public function __construct(SoldierDataFormatter $formatter)
    {
        $this->formatter = $formatter;
    }
    public function index(Request $request)
    {
        // $profiles = Soldier::with(['rank', 'company'])->get();
        // dd($profiles);
        $query = Soldier::query();
        // $profile = $query->paginate(20)->withQueryString();
        if ($request->ajax()) {
            $profiles = Soldier::with(['rank', 'company'])->get();

            return response()->json([
                'data' => $this->formatter->formatCollection($profiles),
                'stats' => [
                    'total' => $profiles->count(),
                    'active' => $profiles->where('is_leave', false)->where('is_sick', false)->count(),
                    'leave' => $profiles->where('is_leave', true)->count(),
                    'medical' => $profiles->where('is_sick', true)->count()
                ]
            ]);
        }
        return view('mpm.page.profile.index');
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
            $profileId = request()->route('soldier') ?? request()->query('id');
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
            return redirect()->route('soldier.serviceForm', $soldier->id)
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


    // <start>*************************Profile service information<start>
    // <start>*************************Profile service information<start>
    // <start>*************************Profile service information<start>
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
        if ($request->redirect) {
            return redirect()->back()->with('success', 'Service information updated successfully');
        } else {
            return redirect()->route('soldier.qualificationsForm', $request->id);
        }
    }
    // <end>*************************Profile service information<end>
    // <end>*************************Profile service information<end>
    // <end>*************************Profile service information<end>


    public function qualificationsForm($id)
    {
        $profile = Soldier::findOrFail($id);
        $profileSteps = $this->getProfileSteps($profile);

        $educations = Education::all();
        $cadres = Cadre::all();
        $courses = Course::all();
        $atts = Atts::all();
        $eres = Eres::all();
        $skills = Skill::all();

        // existing data //
        $educationsData = $profile->educations->map(function ($edu) {
            return [
                'name' => $edu->id,
                'status' => $edu->pivot->result,
                'year' => $edu->pivot->passing_year,
                'remark' => $edu->pivot->remark,
            ];
        });

        $coursesData = $profile->courses->map(function ($data) {
            return [
                'name' => $data->id,
                'status' => $data->pivot->course_status,
                'start_date' => $data->pivot->start_date,
                'end_date' => $data->pivot->end_date,
                'result' => $data->pivot->remarks,
            ];
        });
        $cadresData = $profile->cadres->map(function ($data) {
            return [
                'name' => $data->id,
                'status' => $data->pivot->course_status,
                'start_date' => $data->pivot->start_date,
                'end_date' => $data->pivot->end_date,
                'result' => $data->pivot->remarks,
            ];
        });
        // dd($cadresData);

        $cocurricular = $profile->skills->map(function ($data) {
            return [
                'name' => $data->id,
                'result' => $data->pivot->remarks,
            ];
        });
        $attData = $profile->att->map(function ($data) {
            return [
                'name' => $data->id,
                'start_date' => $data->pivot->start_date,
                'end_date' => $data->pivot->end_date,
            ];
        });
        $ereData = $profile->ere->map(function ($data) {
            return [
                'name' => $data->id,
                'start_date' => $data->pivot->start_date,
                'end_date' => $data->pivot->end_date,
            ];
        });



        $sections = [
            'education' => [
                'label' => 'Education',
                'description' => 'Add your academic qualifications.',
                'options' => $educations,
                'fields' => ['status' => ['Running', 'Passed'], 'year' => 'Year'],
            ],
            'courses' => [
                'label' => 'Courses',
                'description' => 'List any professional courses.',
                'options' => $courses,
                'fields' => [
                    'status' => ['Running', 'Passed'],
                    'start_date' => 'Start Date',
                    'end_date' => 'End Date',
                    'result' => 'Result',
                ],
            ],
            'cadres' => [
                'label' => 'Cadres',
                'description' => 'List any professional cadres.',
                'options' => $cadres,
                'fields' => [
                    'status' => ['Running', 'Passed'],
                    'start_date' => 'Start Date',
                    'end_date' => 'End Date',
                    'result' => 'Result',
                ],
            ],
            'cocurricular' => [
                'label' => 'Co-Curricular Activities',
                'description' => 'Include sports, clubs, etc.',
                'options' => $skills,
                'fields' => ['result' => 'Achievement / Remark'],
            ],
            'ere' => [
                'label' => 'ERE',
                'description' => 'List your ERE history.',
                'options' => $eres,
                'fields' => ['start_date' => 'Start Date', 'end_date' => 'End Date'],
            ],
            'attachments' => [
                'label' => 'Attachments',
                'description' => 'List any attachments.',
                'options' => $atts,
                'fields' => ['start_date' => 'Start Date', 'end_date' => 'End Date'],
            ],
        ];


        return view('mpm.page.profile.qualification', compact(
            'profileSteps',
            'profile',
            'sections',
            'educationsData',
            'coursesData',
            'cadresData',
            'cocurricular',
            'attData',
            'ereData',

        ));
    }


    public function saveQualifications(StoreQualificationRequest  $request)
    {
        $profile = Soldier::findOrFail($request->id);


        // Save education qualifications
        DB::transaction(function () use ($request, $profile) {

            foreach (['soldiers_att', 'soldiers_ere', 'soldier_skills', 'soldier_educations', 'soldier_cadres', 'soldier_courses'] as $table)
                DB::table($table)->where('soldier_id', $request->id)->delete();

            // EDUCATIONS
            if ($request->filled('education')) {
                // dd($request->education);
                foreach ($request->education as $edu) {
                    if (empty($edu['name'])) continue;

                    $profile->educations()->attach($edu['name'], [
                        'remarks'      => $edu['remark'] ?? null,
                        'result'       => $edu['status'] ?? null,
                        'passing_year' => $edu['year'] ?? null,
                    ]);
                }
            }

            // COURSES
            if ($request->filled('courses')) {
                foreach ($request->courses as $course) {
                    if (empty($course['name'])) continue;

                    $profile->courses()->attach($course['name'], [
                        'course_status'   => $course['status'] ?? null,
                        'remarks'         => $course['result'] ?? null,
                        'start_date'      => $course['start_date'] ?? null,
                        'end_date'        => $course['end_date'] ?? null,
                        'completion_date' => null,
                    ]);
                }
            }

            // CADRES
            if ($request->filled('cadres')) {

                foreach ($request->cadres as $cadre) {
                    if (empty($cadre['name'])) continue;

                    $profile->cadres()->attach($cadre['name'], [
                        'course_status'   => $cadre['status'] ?? null,
                        'remarks'         => $cadre['result'] ?? null,
                        'start_date'      => $cadre['start_date'] ?? null,
                        'end_date'        => $cadre['end_date'] ?? null,
                        'completion_date' => null,
                    ]);
                }
            }

            // CO-CURRICULAR
            if ($request->filled('cocurricular')) {

                foreach ($request->cocurricular as $skill) {
                    if (empty($skill['name'])) continue;

                    $profile->skills()->attach($skill['name'], [
                        'remarks'           => $skill['result'] ?? null,
                        'proficiency_level' => 'Beginner'
                    ]);
                }
            }

            // ERE
            if ($request->filled('ere')) {

                foreach ($request->ere as $er) {
                    if (empty($er['name'])) continue;

                    $profile->ere()->attach($er['name'], [
                        'start_date' => $er['start_date'] ?? null,
                        'end_date'   => $er['end_date'] ?? null,
                    ]);
                }
            }

            // ATTACHMENTS
            if ($request->filled('attachments')) {

                foreach ($request->attachments as $att) {
                    if (empty($att['name'])) continue;

                    $profile->att()->attach($att['name'], [
                        'start_date' => $att['start_date'] ?? null,
                        'end_date'   => $att['end_date'] ?? null,
                    ]);
                }
            }
        });


        $profile->update([
            'qualifications_completed' => 1
        ]);

        if ($request->action_update) {
            return redirect()->route('soldier.qualificationsForm', $request->id)->with('success', 'Information updated successfully');
        }
        return redirect()->route('soldier.medicalForm', $request->id)->with('success', 'Information inserted successfully');
    }

    public function medicalForm($id)
    {
        $profile = Soldier::findOrFail($id);
        $profileSteps = $this->getProfileSteps($profile);

        $medicalCategory = MedicalCategory::all();
        $permanentSickness = PermanentSickness::all();

        $soldierMedicalData = $profile->medicalCategory->map(function ($data) {
            return [
                'category'    => $data->pivot->medical_category_id,
                'remarks'     => $data->pivot->remarks,
                'start_date'  => $data->pivot->start_date,
                'end_date'    => $data->pivot->end_date,
            ];
        });

        $soldierSicknessData = $profile->sickness->map(function ($data) {
            return [
                'category' => $data->pivot->permanent_sickness_id,
                'remarks' => $data->pivot->remarks,
                'start_date' => $data->pivot->start_date,
                'end_date' => $data->pivot->end_date,
            ];
        });

        $goodBehevior = $profile->goodDiscipline->map(function ($data) {
            return [
                'name' => $data->discipline_name,
                'remarks' => $data->remarks,
            ];
        });


        $badBehavior = $profile->punishmentDiscipline->map(function ($data) {
            return [
                'name' => $data->discipline_name,
                'start_date'         => $data->start_date,
                'remarks' => $data->remarks,
            ];
        });


        return view(
            'mpm.page.profile.medical',
            compact(
                'profileSteps',
                'profile',
                'medicalCategory',
                'permanentSickness',

                'badBehavior',
                'goodBehevior',
                'soldierSicknessData',
                'soldierMedicalData'
            )
        );
    }

    public function saveMedical(StoreMedicalRequest $request)
    {
        $profile = Soldier::findOrFail($request->id);
        $profileSteps = $this->getProfileSteps($profile);

        DB::transaction(function () use ($request, $profile) {
            // Medical categories
            $syncData = [];
            foreach ($request->medical as $medic) {
                if (empty($medic['category'])) continue;

                $syncData[$medic['category']] = [
                    'remarks'    => $medic['remarks'] ?? null,
                    'start_date' => $medic['start_date'] ?? null,
                    'end_date'   => $medic['end_date'] ?? null,
                ];
            }
            $profile->medicalCategory()->sync($syncData);

            // sickness data
            $syncDataSickness = [];
            foreach ($request->sickness as $sickness) {
                if (empty($sickness['category'])) continue;
                $syncDataSickness[$sickness['category']] = [
                    'remarks'    => $sickness['remarks'] ?? null,
                    'start_date' => $sickness['start_date'] ?? null,
                ];
            }
            // dd($syncDataSickness);
            $profile->sickness()->sync($syncDataSickness);

            // discipline data table
            $profile->discipline()->delete(); // delete existing

            if ($request->good_behavior) {
                $profile->discipline()->create([
                    'discipline_name' => $request->good_behavior,
                    'discipline_type' => 'good',
                ]);
            }


            if ($request->filled('punishments')) {
                foreach ($request->punishments as $punishments) {
                    if (!empty($punishments['type'])) {
                        $profile->discipline()->create([
                            'discipline_name' => $punishments['type'],
                            'discipline_type' => 'punishment',
                            'remarks'         => $punishments['remarks'] ?? null,
                            'start_date'         => $punishments['date'] ?? null,

                        ]);
                    }
                }
            }
            $profile->update([
                'medical_completed' => 1
            ]);
        });

        if ($request->action_update) {
            return redirect()->route('soldier.medicalForm', $request->id)->with('success', 'Information updated successfully');
        }

        return redirect()->route('soldier.index', $request->id)
            ->with('success', 'Profile completed successfully!');
    }
    public function details($id)
    {
        $profile = Soldier::findOrFail($id);

        // Separate current and previous appointments
        $current  = $profile->services->where('appointment_type', 'current')->last();
        $previous = $profile->services->where('appointment_type', 'previous');

        // Use service methods
        $educationsData      = $this->formatter->formatEducations($profile);
        $coursesData         = $this->formatter->formatCourses($profile);
        $cadresData          = $this->formatter->formatCadres($profile);
        $cocurricular        = $this->formatter->formatSkills($profile);
        $attData             = $this->formatter->formatAtt($profile);
        $ereData             = $this->formatter->formatEre($profile);
        $soldierMedicalData  = $this->formatter->formatMedical($profile);
        $soldierSicknessData = $this->formatter->formatSickness($profile);
        $goodBehevior        = $this->formatter->formatGoodDiscipline($profile);
        $badBehavior         = $this->formatter->formatBadDiscipline($profile);

        return view('mpm.page.profile.details', compact(
            'profile',
            'current',
            'previous',
            'educationsData',
            'coursesData',
            'cadresData',
            'cocurricular',
            'attData',
            'ereData',
            'soldierMedicalData',
            'soldierSicknessData',
            'goodBehevior',
            'badBehavior'
        ));
    }
    private function getProfileSteps($profile = null)
    {
        $profileId = $profile?->id;

        return [
            [
                'key'       => 'personal',
                'title'     => 'Personal',
                'routeName' => 'soldier.personalForm',
                'params'    => $profileId ? ['id' => $profileId] : [],
                'icon'      => $profile?->personal_completed ? '✔' : '1',
                'enabled'   => true,
                'completed' => $profile?->personal_completed ?? false,
            ],
            [
                'key'       => 'service',
                'title'     => 'Service',
                'routeName' => $profileId && $profile?->personal_completed
                    ? 'soldier.serviceForm'
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
                    ? 'soldier.qualificationsForm'
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
                    ? 'soldier.medicalForm'
                    : null,
                'params'    => $profileId && $profile?->qualifications_completed ? ['id' => $profileId] : [],

                'icon'      => $profile?->medical_completed ? '✔' : '4',

                'enabled'   => $profile?->qualifications_completed ?? false,
                'completed' => $profile?->medical_completed ?? false,
            ],
        ];
    }
    public function getByRank($rankId)
    {
        $soldiers = Soldier::with('rank') // ✅ just eager load the rank
            ->where('rank_id', $rankId)   // ✅ filter by rank
            ->where('is_leave', false)
            ->where('status', 'active')   // optional: only active soldiers
            ->where('is_sick', false)     // optional: not sick
            ->get(['id', 'full_name', 'army_no']);

        return response()->json($soldiers);
    }

    /// updated code:
    /**
     * Delete a soldier profile
     */
    public function destroy(Soldier $soldier)
    {
        try {
            // Delete associated image if exists
            if ($soldier->image && \Storage::disk('public')->exists($soldier->image)) {
                \Storage::disk('public')->delete($soldier->image);
            }

            $soldier->delete();

            return response()->json(['success' => true, 'message' => 'Profile deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Error deleting soldier: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete profile'], 500);
        }
    }

    /**
     * Export soldiers data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $selectedIds = $request->get('selected') ? explode(',', $request->get('selected')) : [];

        $query = Soldier::with(['rank', 'company']);

        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('army_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('rank')) {
            $query->whereHas('rank', function ($q) use ($request) {
                $q->where('name', $request->get('rank'));
            });
        }

        if ($request->filled('company')) {
            $query->whereHas('company', function ($q) use ($request) {
                $q->where('name', $request->get('company'));
            });
        }

        $soldiers = $query->get();

        if ($format === 'excel') {
            return $this->exportToExcel($soldiers);
        } else {
            return $this->exportToPdf($soldiers);
        }
    }

    /**
     * Bulk update soldier status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'soldier_ids' => 'required|array',
            'soldier_ids.*' => 'exists:soldiers,id',
            'status' => 'required|in:active,leave,medical,inactive'
        ]);

        try {
            $soldierIds = $request->get('soldier_ids');
            $status = $request->get('status');

            // Update based on status
            $updateData = ['status' => 'active'];

            if ($status === 'leave') {
                $updateData['is_leave'] = true;
                $updateData['is_sick'] = false;
            } elseif ($status === 'medical') {
                $updateData['is_sick'] = true;
                $updateData['is_leave'] = false;
            } elseif ($status === 'inactive') {
                $updateData['status'] = 'inactive';
                $updateData['is_leave'] = false;
                $updateData['is_sick'] = false;
            } else {
                $updateData['is_leave'] = false;
                $updateData['is_sick'] = false;
            }

            Soldier::whereIn('id', $soldierIds)->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Status updated for ' . count($soldierIds) . ' soldiers'
            ]);
        } catch (\Exception $e) {
            \Log::error('Bulk update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }

    /**
     * Bulk delete soldiers
     */
    public function bulkDelete(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'soldier_ids' => 'required|array',
            'soldier_ids.*' => 'exists:soldiers,id'
        ]);

        try {
            $soldierIds = $request->get('soldier_ids');
            $soldiers = Soldier::whereIn('id', $soldierIds)->get();

            // Delete images
            foreach ($soldiers as $soldier) {
                if ($soldier->image && \Storage::disk('public')->exists($soldier->image)) {
                    \Storage::disk('public')->delete($soldier->image);
                }
            }

            // Delete soldiers

            Soldier::whereIn('id', $soldierIds)->delete();

            return response()->json([
                'success' => true,
                'message' => count($soldierIds) . ' profiles deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete profiles'], 500);
        }
    }

    private function exportToExcel($soldiers)
    {
        // You'll need to install maatwebsite/excel for this
        // composer require maatwebsite/excel

        $filename = 'soldiers_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($soldiers) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Army No',
                'Full Name',
                'Rank',
                'Company',
                'Status',
                'Joining Date',
                'Profile Completion',
                'Phone',
                'Email'
            ]);

            // Data
            foreach ($soldiers as $soldier) {
                $completion = $this->calculateProfileCompletion($soldier);
                $status = $this->getStatusText($soldier);

                fputcsv($file, [
                    $soldier->army_no,
                    $soldier->full_name,
                    $soldier->rank->name ?? 'N/A',
                    $soldier->company->name ?? 'N/A',
                    $status,
                    $soldier->joining_date ? $soldier->joining_date->format('Y-m-d') : 'N/A',
                    $completion . '%',
                    $soldier->phone ?? 'N/A',
                    $soldier->email ?? 'N/A'
                ]);
            }

            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function exportToPdf($soldiers)
    {
        // You'll need to install a PDF library like dompdf
        // For now, return CSV with PDF headers
        return $this->exportToExcel($soldiers);
    }

    private function calculateProfileCompletion($soldier)
    {
        $completedSteps = collect([
            $soldier->personal_completed,
            $soldier->service_completed,
            $soldier->qualifications_completed,
            $soldier->medical_completed
        ])->filter()->count();

        return round(($completedSteps / 4) * 100);
    }

    private function getStatusText($soldier)
    {
        if ($soldier->is_leave) return 'On Leave';
        if ($soldier->is_sick) return 'Medical';
        if ($soldier->status === 'active') return 'Active';
        return 'Inactive';
    }
    public function getProfileData(Soldier $soldier)
    {
        // Load relationships you need for the modal
        $soldier->load(['rank', 'company']);

        return response()->json($soldier);
    }
}
