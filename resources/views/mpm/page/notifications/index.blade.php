@extends('mpm.layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Notifications</h1>
                            <p class="text-blue-100 text-sm mt-1">
                                {{ $notifications->total() }} total notifications
                                @if ($notifications->where('read_at', null)->count())
                                    <span class="ml-2 px-2 py-1 bg-blue-500 text-white text-xs rounded-full">
                                        {{ $notifications->where('read_at', null)->count() }} unread
                                    </span>
                                @endif
                            </p>
                        </div>
                        <button id="mark-all-read"
                            class="inline-flex items-center px-4 py-2 bg-white text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Mark All as Read
                        </button>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="divide-y divide-gray-200">
                    @forelse ($notifications as $notification)
                        <div
                            class="notification-item px-6 py-4 transition-all duration-200 ease-in-out hover:bg-gray-50
                                    {{ is_null($notification->read_at) ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <!-- Notification Header -->
                                    <div class="flex items-center space-x-2 mb-2">
                                        @if (str_contains($notification->type, 'LeaveCompleted'))
                                            <div
                                                class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Leave Completed
                                            </span>
                                        @else
                                            <div
                                                class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Leave Approved
                                            </span>
                                        @endif

                                        @if (is_null($notification->read_at))
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                New
                                            </span>
                                        @endif

                                        <span class="text-xs text-gray-500 ml-auto">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <!-- Notification Message -->
                                    <p class="text-gray-900 font-medium mb-3 text-lg leading-relaxed">
                                        {{ $notification->data['message'] }}
                                    </p>

                                    <!-- Notification Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                        <div class="flex items-center space-x-2 text-gray-600">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                            <span><strong>Soldier:</strong> {{ $notification->data['soldier_name'] }}</span>
                                        </div>

                                        <div class="flex items-center space-x-2 text-gray-600">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <span><strong>Period:</strong> {{ $notification->data['start_date'] }} to
                                                {{ $notification->data['end_date'] }}</span>
                                        </div>

                                        <div class="flex items-center space-x-2 text-gray-600">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                </path>
                                            </svg>
                                            <span><strong>Type:</strong> {{ $notification->data['leave_type'] }}</span>
                                        </div>

                                        <div class="flex items-center space-x-2 text-gray-600">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                            <span><strong>ID:</strong>
                                                #{{ $notification->data['leave_application_id'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                @if (is_null($notification->read_at))
                                    <button
                                        class="mark-as-read ml-4 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                        data-id="{{ $notification->id }}">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Mark Read
                                    </button>
                                @else
                                    <div
                                        class="ml-4 inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-500 bg-white cursor-default">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Read
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                            <p class="text-gray-500 max-w-sm mx-auto">
                                You're all caught up! When new notifications arrive, they'll appear here.
                            </p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if ($notifications->hasPages())
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mark as read
            document.querySelectorAll('.mark-as-read').forEach(button => {
                button.addEventListener('click', function() {
                    const notificationId = this.dataset.id;
                    const button = this;

                    // Add loading state
                    button.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    `;
                    button.disabled = true;

                    fetch(`/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Remove the blue background and border
                                const notificationItem = button.closest('.notification-item');
                                notificationItem.classList.remove('bg-blue-50', 'border-l-4',
                                    'border-l-blue-500');

                                // Replace button with read state
                                button.outerHTML = `
                                <div class="ml-4 inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-500 bg-white cursor-default">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Read
                                </div>
                            `;

                                // Update unread count in header if exists
                                const unreadBadge = document.querySelector('.bg-red-100');
                                if (unreadBadge) {
                                    const currentCount = parseInt(unreadBadge.textContent);
                                    if (currentCount > 1) {
                                        unreadBadge.textContent = `${currentCount - 1} unread`;
                                    } else {
                                        unreadBadge.remove();
                                    }
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Reset button on error
                            button.innerHTML = `
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark Read
                        `;
                            button.disabled = false;
                            alert('Failed to mark notification as read. Please try again.');
                        });
                });
            });

            // Mark all as read
            document.getElementById('mark-all-read').addEventListener('click', function() {
                const button = this;

                // Add loading state
                const originalText = button.innerHTML;
                button.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-700" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
                button.disabled = true;

                fetch('/notifications/read-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Remove all unread styles
                            document.querySelectorAll('.notification-item').forEach(item => {
                                item.classList.remove('bg-blue-50', 'border-l-4',
                                    'border-l-blue-500');
                            });

                            // Replace all mark-as-read buttons with read state
                            document.querySelectorAll('.mark-as-read').forEach(button => {
                                button.outerHTML = `
                                <div class="ml-4 inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-500 bg-white cursor-default">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Read
                                </div>
                            `;
                            });

                            // Remove unread badge from header
                            const unreadBadge = document.querySelector('.bg-red-100');
                            if (unreadBadge) {
                                unreadBadge.remove();
                            }
                        }

                        // Reset button
                        button.innerHTML = originalText;
                        button.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Reset button on error
                        button.innerHTML = originalText;
                        button.disabled = false;
                        alert('Failed to mark all notifications as read. Please try again.');
                    });
            });
        });
    </script>
@endpush
