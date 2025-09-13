export class SoldierBulkActions {
    constructor(manager) {
        this.manager = manager; // SoldierProfileManager instance
        this.selectedRows = manager.selectedRows;
    }

    toggleSelectAll(checked) {
        document.querySelectorAll('.row-select').forEach(cb => {
            cb.checked = checked;
            const id = cb.value;
            if (checked) this.selectedRows.add(id);
            else this.selectedRows.delete(id);
        });
        this.updateBulkActionButton();
    }

    updateBulkActionButton() {
        const bulkButton = document.getElementById('bulk-action');
        if (!bulkButton) return;
        if (this.selectedRows.size > 0) {
            bulkButton.classList.remove('hidden');
            bulkButton.textContent = `Bulk Actions (${this.selectedRows.size})`;
        } else {
            bulkButton.classList.add('hidden');
        }
    }

    showBulkActions() {
        const actions = [
            { label: 'Export Selected', action: 'export' },
            { label: 'Delete Selected', action: 'delete', dangerous: true }
        ];

        let html = '<div class="space-y-2">';
        actions.forEach(a => {
            const colorClass = a.dangerous ? 'text-red-600 hover:bg-red-50' : 'text-gray-700 hover:bg-gray-100';
            html += `<button class="bulk-action-btn w-full text-left px-4 py-2 text-sm ${colorClass} rounded-md transition-colors duration-200" data-action="${a.action}">${a.label}</button>`;
        });
        html += '</div>';

        this.manager.showModal('Bulk Actions', html);

        // Attach event listeners
        const modal = document.getElementById('bulk-modal');
        modal.querySelectorAll('.bulk-action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const action = btn.getAttribute('data-action');
                this.performBulkAction(action);
            });
        });
    }

    performBulkAction(action) {
        const count = this.selectedRows.size;
        switch (action) {
            case 'export':
                this.exportSelected();
                break;
            case 'delete':
                if (confirm(`Delete ${count} profiles?`)) {
                    this.bulkDelete();
                }
                break;
        }
        this.manager.closeModal('bulk-modal');
    }

    exportSelected() {
        const params = new URLSearchParams({ selected: Array.from(this.selectedRows).join(','), format: 'excel' });
        window.open(`/army/export?${params}`, '_blank');
    }

    async bulkDelete() {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const res = await fetch(routes.bulkDelete, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ soldier_ids: Array.from(this.selectedRows) })
            });

            if (res.ok) {
                const count = this.selectedRows.size;
                this.selectedRows.clear();
                await this.manager.loadData();
                this.manager.showSuccess(`${count} profiles deleted`);
            } else {
                const err = await res.json().catch(() => ({}));
                console.error('Bulk delete failed:', err);
                this.manager.showError('Failed to delete profiles');
            }
        } catch (err) {
            console.error(err);
            this.manager.showError('Failed to delete profiles');
        }
    }

    async deleteProfile(soldierId) {
        if (!confirm('Are you sure you want to delete this soldier profile? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch(routes.delete.replace(':id', soldierId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                await this.manager.loadData();
                this.manager.showSuccess('Profile deleted successfully');
            } else {
                throw new Error('Delete failed');
            }
        } catch (error) {
            console.error('Error deleting profile:', error);
            this.manager.showError('Failed to delete profile');
        }
    }
}
