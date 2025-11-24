<!-- Complete Course Confirmation Modal -->
<div id="completeCourseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Complete Course</h3>
                <button onclick="closeCompleteCourseModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-center text-gray-700 mb-4">
                    Are you sure you want to mark <span id="soldierNameCompleteCourse" class="font-semibold"></span>'s
                    course as completed?
                </p>
                <p class="text-center text-sm text-gray-500">
                    This will move their course assignment to previous courses.
                </p>
            </div>

            <!-- Form -->
            <form id="completeCourseForm" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="action" value="complete">

                <!-- Recommendation Dropdown -->
                <div class="mb-4">
                    <label for="completeCourseRecommendation" class="block text-sm font-medium text-gray-700 mb-1">
                        instr
                    </label>
                    <select name="recommendation_id" id="completeCourseRecommendation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Qualified For</option>
                        @foreach ($instructionRecomendations ?? [] as $recommendation)
                            <option value="{{ $recommendation->id }}">{{ $recommendation->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Completion Note -->
                <div class="mb-6">
                    <label for="completeCourseNote" class="block text-sm font-medium text-gray-700 mb-1">
                        Remarks (Optional)
                    </label>
                    <textarea name="completion_note" id="completeCourseNote" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                        placeholder="Additional comments or remarks..."></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCompleteCourseModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors duration-200">
                        Mark as Completed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Cadre Confirmation Modal -->
<div id="completeCadreModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Complete Cadre</h3>
                <button onclick="closeCompleteCadreModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-center text-gray-700 mb-4">
                    Are you sure you want to mark <span id="soldierNameCompleteCadre" class="font-semibold"></span>'s
                    cadre as completed?
                </p>
                <p class="text-center text-sm text-gray-500">
                    This will move their cadre assignment to previous cadres.
                </p>
            </div>

            <!-- Form -->
            <form id="completeCadreForm" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="action" value="complete">

                <!-- Recommendation Dropdown -->
                <div class="mb-4">
                    <label for="completeCadreRecommendation" class="block text-sm font-medium text-gray-700 mb-1">
                        instr
                    </label>
                    <select name="recommendation_id" id="completeCadreRecommendation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Qualified For</option>
                        @foreach ($instructionRecomendations ?? [] as $recommendation)
                            <option value="{{ $recommendation->id }}">{{ $recommendation->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Completion Note -->
                <div class="mb-6">
                    <label for="completeCadreNote" class="block text-sm font-medium text-gray-700 mb-1">
                        Remarks (Optional)
                    </label>
                    <textarea name="completion_note" id="completeCadreNote" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                        placeholder="Additional comments or remarks..."></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCompleteCadreModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors duration-200">
                        Mark as Completed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Ex-Area Confirmation Modal -->
<div id="completeExAreaModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Complete Ex-Area</h3>
                <button onclick="closeCompleteExAreaModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-center text-gray-700 mb-4">
                    Are you sure you want to mark <span id="soldierNameCompleteExArea" class="font-semibold"></span>'s
                    ex-area as completed?
                </p>
                <p class="text-center text-sm text-gray-500">
                    This will move their ex-area assignment to previous ex-areas.
                </p>
            </div>

            <!-- Form -->
            <form id="completeExAreaForm" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="action" value="complete">

                <!-- Recommendation Dropdown -->
                <div class="mb-4">
                    <label for="completeExAreaRecommendation" class="block text-sm font-medium text-gray-700 mb-1">
                        instr
                    </label>
                    <select name="recommendation_id" id="completeExAreaRecommendation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Qualified For</option>
                        @foreach ($instructionRecomendations ?? [] as $recommendation)
                            <option value="{{ $recommendation->id }}">{{ $recommendation->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Completion Note -->
                <div class="mb-6">
                    <label for="completeExAreaNote" class="block text-sm font-medium text-gray-700 mb-1">
                        Remarks (Optional)
                    </label>
                    <textarea name="completion_note" id="completeExAreaNote" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                        placeholder="Additional comments or remarks..."></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCompleteExAreaModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors duration-200">
                        Mark as Completed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Error</h3>
                <button onclick="closeErrorModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-center text-gray-700 mb-4" id="errorMessage">
                    An error occurred while processing your request.
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeErrorModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Assignment Modal -->
<div id="editAssignmentModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex justify-between items-center mb-4 border-b pb-3">
            <h3 class="text-lg font-medium text-gray-900" id="editModalTitle">Edit Assignment</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="mb-6">
            <form id="editAssignmentForm" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" id="editAssignmentId" name="assignment_id">
                <input type="hidden" id="editAssignmentType" name="type">

                <!-- Assignment Type Display -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assignment Type</label>
                    <div id="editAssignmentTypeDisplay"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900 font-medium">
                    </div>
                </div>

                <!-- Date Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Start Date -->
                    <div>
                        <label for="editStartDate" class="block text-sm font-medium text-gray-700 mb-2">Start
                            Date</label>
                        <input type="date" id="editStartDate" name="start_date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="editEndDate" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" id="editEndDate" name="end_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Course Selection (shown only for courses) -->
                <div id="editCourseSection" class="hidden">
                    <label for="editCourseId" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                    <select id="editCourseId" name="course_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <!-- Options will be loaded dynamically -->
                    </select>
                </div>

                <!-- Cadre Selection (shown only for cadres) -->
                <div id="editCadreSection" class="hidden">
                    <label for="editCadreId" class="block text-sm font-medium text-gray-700 mb-2">Cadre</label>
                    <select id="editCadreId" name="cadre_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <!-- Options will be loaded dynamically -->
                    </select>
                </div>

                <!-- Ex-Area Selection (shown only for ex-areas) -->
                <div id="editExAreaSection" class="hidden">
                    <label for="editExAreaId" class="block text-sm font-medium text-gray-700 mb-2">Ex-Area</label>
                    <select id="editExAreaId" name="ex_area_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <!-- Options will be loaded dynamically -->
                    </select>
                </div>

                <!-- Soldier Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Soldier</label>
                    <select id="editSoldierId" name="soldier_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <!-- Options will be loaded dynamically -->
                    </select>
                </div>

                <!-- Notes Section -->
                <div>
                    <label for="editNote" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea id="editNote" name="note" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <!-- Date Adjustment Warning -->
                <div id="editDateAdjustmentWarning" class="hidden p-3 bg-amber-50 rounded-lg border border-amber-200">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-500 mt-0.5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <p class="text-sm text-amber-700">
                            The start date will be adjusted to tomorrow if the soldier has completed assignments today.
                        </p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors duration-200">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
