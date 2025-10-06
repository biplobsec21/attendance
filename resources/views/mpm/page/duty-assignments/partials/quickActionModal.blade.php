<!-- Quick Action Modal -->
<div id="quickActionModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-indigo-600">
            <h3 class="text-lg font-semibold text-white" id="quickActionTitle">Quick Action</h3>
            <button onclick="closeQuickActionModal()" class="text-white hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div id="quickActionContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>
