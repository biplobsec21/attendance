@extends('mpm.layouts.app')

@section('title', 'Permissions Management')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- ==== Left: Manage Permissions ==== --}}
            <div class="bg-white shadow-md rounded-lg flex flex-col max-h-[600px]">

                {{-- Add Permission Form Sticky --}}
                <div class="p-6 bg-white z-10 sticky top-0 border-b">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Manage Permissions</h2>

                    @if (session('success'))
                        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 border border-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded bg-red-100 text-red-700 border border-red-400">
                            <ul class="list-disc pl-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('permissions.store') }}" method="POST" class="flex gap-2"
                        onsubmit="disableButton(this)">
                        @csrf
                        <input type="text" name="name" placeholder="Enter permission name"
                            class="flex-1 border-gray-300 rounded-lg px-3 py-2" required>
                        <button type="submit" id="submitBtn"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                            Add
                        </button>
                    </form>
                </div>

                {{-- Scrollable Table with custom scrollbar --}}
                <div class="overflow-y-auto flex-1 custom-scrollbar">
                    <table class="table-auto w-full border-collapse">
                        <thead class="bg-gray-100 sticky top-0 z-10">
                            <tr>
                                <th class="px-3 py-2 border">#</th>
                                <th class="px-3 py-2 border">Permission</th>
                                <th class="px-3 py-2 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissionsFlat as $permission)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 border">{{ $loop->iteration }}</td>
                                    <td class="px-3 py-2 border">{{ $permission->name }}</td>
                                    <td class="px-3 py-2 border flex gap-2">
                                        {{-- Edit --}}
                                        <form action="{{ route('permissions.update', $permission->id) }}" method="POST"
                                            class="flex gap-2" onsubmit="disableButton(this)">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" value="{{ $permission->name }}"
                                                class="border-gray-300 rounded px-2 py-1 w-40">
                                            <button type="submit"
                                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                                Update
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this permission?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ==== Right: Assign Permissions to Role ==== --}}
            <div class="bg-white shadow-md rounded-lg p-6 flex flex-col">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Assign Permissions to Role</h2>

                <div id="alert-box" class="hidden mb-4 p-3 rounded-lg"></div>

                <div class="mb-4">
                    <label class="block mb-2 font-medium text-gray-700">Select Role</label>
                    <select id="roleSelect" class="w-full border-gray-300 rounded-lg px-3 py-2">
                        <option value="">-- Select Role --</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->id }}">{{ ucfirst($r->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col flex-1">
                    <div class="overflow-y-auto max-h-[400px] border rounded-lg p-4 custom-scrollbar">
                        <form id="permissionForm" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div id="permissionsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach ($permissions as $module => $perms)
                                    <div class="p-4 border rounded-lg bg-gray-50">
                                        <h3 class="font-semibold text-gray-700 mb-2">{{ ucfirst($module) }}</h3>
                                        <div class="space-y-1">
                                            @foreach ($perms as $permission)
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="permissions[]"
                                                        value="{{ $permission->name }}"
                                                        class="perm-checkbox form-checkbox h-4 w-4 text-blue-500">
                                                    <span class="text-gray-700">{{ $permission->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </form>
                    </div>

                    <div class="mt-4">
                        <button type="submit" form="permissionForm"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg w-full">
                            Save Permissions
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom scrollbar for both sections */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            /* Tailwind slate-300 */
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
            /* Tailwind slate-400 */
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const roleSelect = document.getElementById("roleSelect");
            const permissionForm = document.getElementById("permissionForm");
            const alertBox = document.getElementById("alert-box");
            let currentRoleId = null;

            function showAlert(type, message) {
                alertBox.className =
                    `mb-4 p-3 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'}`;
                alertBox.textContent = message;
                alertBox.classList.remove("hidden");
                setTimeout(() => alertBox.classList.add("hidden"), 3000);
            }

            roleSelect.addEventListener("change", function() {
                currentRoleId = this.value;
                if (!currentRoleId) {
                    permissionForm.classList.add("hidden");
                    return;
                }

                fetch(`/admin/permissions/role/${currentRoleId}`)
                    .then(res => res.json())
                    .then(data => {
                        document.querySelectorAll(".perm-checkbox").forEach(cb => cb.checked = false);
                        data.permissions.forEach(p => {
                            const checkbox = document.querySelector(
                                `.perm-checkbox[value="${p}"]`);
                            if (checkbox) checkbox.checked = true;
                        });
                        permissionForm.classList.remove("hidden");
                    });
            });

            permissionForm.addEventListener("submit", function(e) {
                e.preventDefault();
                if (!currentRoleId) return;

                const formData = new FormData(this);
                fetch(`/admin/permissions/role/${currentRoleId}`, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": formData.get('_token'),
                            "X-HTTP-Method-Override": "PUT"
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) showAlert('success', data.message);
                        else showAlert('error', 'Something went wrong.');
                    })
                    .catch(() => showAlert('error', 'Error saving permissions.'));
            });

            function disableButton(form) {
                form.querySelector('#submitBtn').disabled = true;
                return true;
            }
        });
    </script>
@endpush
