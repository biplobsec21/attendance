<!-- Assign Duties Modal -->
<div id="assignModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-blue-600">
            <h3 class="text-lg font-semibold text-white">Assign Duties</h3>
            <button onclick="closeAssignModal()" class="text-white hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <label class="inline-flex items-center mr-6">
                    <input type="radio" name="assignType" value="single" checked onchange="toggleAssignType()"
                        class="form-radio text-blue-600">
                    <span class="ml-2">Single Date</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="assignType" value="range" onchange="toggleAssignType()"
                        class="form-radio text-blue-600">
                    <span class="ml-2">Date Range</span>
                </label>
            </div>

            <form id="assignSingleForm" onsubmit="handleAssignSingle(event)">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                    <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Assign Duties
                </button>
            </form>

            <form id="assignRangeForm" onsubmit="handleAssignRange(event)" class="hidden">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" required min="<?php echo date('Y-m-d'); ?>"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" required min="<?php echo date('Y-m-d'); ?>"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Assign Date Range
                </button>
            </form>
        </div>
    </div>
</div>
