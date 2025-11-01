/**
 * UPDATED: SoldierBulkActions class - Works with visible soldiers only
 * Complete file: soldierBulkActions.js
 */

import { showToast } from "./soldierHelpers.js";

export class SoldierBulkActions {
    constructor(manager) {
        this.manager = manager;
        this.init();
    }

    init() {
        const bulkActionBtn = document.getElementById('bulk-action');
        if (bulkActionBtn) {
            bulkActionBtn.addEventListener('click', () => {
                this.showBulkActionModal();
            });
        }
    }

    /**
     * UPDATED: Select all now only selects visible/filtered soldiers
     */
    toggleSelectAll(checked) {
        console.log(`🔄 Toggle Select All: ${checked}`);
        console.log(`📊 Visible soldiers: ${this.manager.filteredSoldiers.length}`);
        console.log(`📊 Total soldiers: ${this.manager.soldiers.length}`);

        if (checked) {
            // Select only currently visible soldiers
            let selectedCount = 0;
            this.manager.filteredSoldiers.forEach(soldier => {
                this.manager.selectedRows.add(soldier.id.toString());
                selectedCount++;
            });

            console.log(`✅ Selected ${selectedCount} visible soldiers`);
            showToast(`Selected ${selectedCount} visible soldiers`, 'success');
        } else {
            // Deselect all
            const previousCount = this.manager.selectedRows.size;
            this.manager.selectedRows.clear();

            console.log(`❌ Deselected ${previousCount} soldiers`);
            showToast('All selections cleared', 'info');
        }

        // Update all checkboxes
        this.updateAllCheckboxes();
        this.updateBulkActionButton();
    }

    /**
     * NEW: Update all checkbox states based on selection
     */
    updateAllCheckboxes() {
        const checkboxes = document.querySelectorAll('.row-select');
        checkboxes.forEach(checkbox => {
            const soldierId = checkbox.value;
            checkbox.checked = this.manager.selectedRows.has(soldierId);
        });
    }

    /**
     * UPDATED: Show/hide bulk action button based on selection
     */
    updateBulkActionButton() {
        const bulkActionBtn = document.getElementById('bulk-action');
        const selectedCount = this.manager.selectedRows.size;

        if (bulkActionBtn) {
            if (selectedCount > 0) {
                bulkActionBtn.classList.remove('hidden');
                bulkActionBtn.classList.add('flex');

                // Update button text with count
                const buttonText = bulkActionBtn.querySelector('span');
                if (buttonText) {
                    buttonText.textContent = `Bulk Actions (${selectedCount})`;
                }
            } else {
                bulkActionBtn.classList.add('hidden');
                bulkActionBtn.classList.remove('flex');
            }
        }

        console.log(`📊 Bulk action button: ${selectedCount} selected`);
    }

    /**
     * Show bulk action modal with options
     */
    showBulkActionModal() {
        const selectedCount = this.manager.selectedRows.size;
        const stats = this.manager.getSelectionStats();

        if (selectedCount === 0) {
            showToast('Please select at least one soldier', 'warning');
            return;
        }

        const modalContent = `
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-blue-900">Selection Summary</span>
                        <span class="text-xs text-blue-600">${stats.percentage}% of visible</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">${stats.selected}</div>
                            <div class="text-xs text-blue-800">Selected</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-600">${stats.visible}</div>
                            <div class="text-xs text-gray-600">Visible</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-400">${stats.total}</div>
                            <div class="text-xs text-gray-500">Total</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <button onclick="bulkActions.exportSelected()"
                        class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-file-excel"></i>
                        <span>Export Selected to Excel</span>
                    </button>

                    <button onclick="bulkActions.exportSelectedPDF()"
                        class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-file-pdf"></i>
                        <span>Export Selected to PDF</span>
                    </button>

                    <button onclick="bulkActions.confirmBulkDelete()"
                        class="w-full px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i>
                        <span>Delete Selected (${selectedCount})</span>
                    </button>

                    <button onclick="bulkActions.deselectAll()"
                        class="w-full px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        <span>Clear Selection</span>
                    </button>
                </div>

                <div class="text-xs text-gray-500 text-center mt-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    Actions will only affect the ${selectedCount} selected soldier${selectedCount > 1 ? 's' : ''}
                </div>
            </div>
        `;

        this.manager.showModal(`Bulk Actions (${selectedCount} selected)`, modalContent);
    }

    /**
     * Deselect all soldiers
     */
    deselectAll() {
        this.manager.selectedRows.clear();

        if (this.manager.elements.selectAll) {
            this.manager.elements.selectAll.checked = false;
            this.manager.elements.selectAll.indeterminate = false;
        }

        this.updateAllCheckboxes();
        this.updateBulkActionButton();
        this.manager.closeModal('bulk-modal');

        showToast('All selections cleared', 'info');
    }

    /**
     * Export selected soldiers to Excel
     */
    exportSelected() {
        const selectedSoldiers = this.manager.getSelectedSoldiers();

        if (selectedSoldiers.length === 0) {
            showToast('No soldiers selected', 'warning');
            return;
        }

        try {
            const exportData = selectedSoldiers.map(soldier => ({
                'Army No': soldier.army_no || '',
                'Name': soldier.name || '',
                'Rank': soldier.rank || '',
                'Company': soldier.unit || '',
                'Mobile': soldier.mobile || '',
                'Blood Group': soldier.blood_group || '',
                'District': soldier.districts || '',
                'Skills': soldier.cocurricular?.map(s => s.name).join(', ') || '',
                'Courses': soldier.courses?.map(c => c.name).join(', ') || '',
                'Cadres': soldier.cadres?.map(c => c.name).join(', ') || '',
                'ATT': soldier.att?.map(a => a.name).join(', ') || '',
                'Education': soldier.educations?.map(e => e.name).join(', ') || '',
                'ERE': soldier.ere?.map(e => e.name).join(', ') || '',
                'CMD': soldier.cmd?.map(c => c.name).join(', ') || '',
                'Ex-Areas': soldier.ex_areas?.map(e => e.name).join(', ') || ''
            }));

            const ws = XLSX.utils.json_to_sheet(exportData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Selected Soldiers');

            const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
            const filename = `soldiers_selected_${timestamp}.xlsx`;

            XLSX.writeFile(wb, filename);

            showToast(`Successfully exported ${selectedSoldiers.length} selected soldiers`, 'success');
            this.manager.closeModal('bulk-modal');
        } catch (error) {
            console.error('Excel export error:', error);
            showToast('Failed to export: ' + error.message, 'error');
        }
    }

    /**
     * Export selected soldiers to PDF
     */
    exportSelectedPDF() {
        const selectedSoldiers = this.manager.getSelectedSoldiers();

        if (selectedSoldiers.length === 0) {
            showToast('No soldiers selected', 'warning');
            return;
        }

        try {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: 'a4'
            });

            doc.setFontSize(18);
            doc.setFont('helvetica', 'bold');
            doc.text('Selected Soldier Profiles', 15, 15);

            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');
            const exportDate = new Date().toLocaleString();
            doc.text(`Generated: ${exportDate} | Selected: ${selectedSoldiers.length}`, 15, 22);

            const tableData = selectedSoldiers.map(soldier => [
                soldier.army_no || '',
                soldier.name || '',
                soldier.rank || '',
                soldier.unit || '',
                soldier.mobile || '',
                soldier.blood_group || '',
                soldier.cocurricular?.map(s => s.name).join(', ').substring(0, 20) || '',
                soldier.courses?.map(c => c.name).join(', ').substring(0, 20) || ''
            ]);

            doc.autoTable({
                startY: 28,
                head: [['Army No', 'Name', 'Rank', 'Company', 'Mobile', 'Blood', 'Skills', 'Courses']],
                body: tableData,
                styles: { fontSize: 8, cellPadding: 2 },
                headStyles: { fillColor: [34, 197, 94], textColor: 255, fontStyle: 'bold' },
                alternateRowStyles: { fillColor: [240, 240, 240] },
                columnStyles: {
                    0: { cellWidth: 20 }, 1: { cellWidth: 40 }, 2: { cellWidth: 20 },
                    3: { cellWidth: 15 }, 4: { cellWidth: 25 }, 5: { cellWidth: 15 },
                    6: { cellWidth: 40 }, 7: { cellWidth: 40 }
                },
                margin: { top: 28, left: 15, right: 15 }
            });

            const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
            const filename = `soldiers_selected_${timestamp}.pdf`;

            doc.save(filename);

            showToast(`Successfully exported ${selectedSoldiers.length} selected soldiers`, 'success');
            this.manager.closeModal('bulk-modal');
        } catch (error) {
            console.error('PDF export error:', error);
            showToast('Failed to export: ' + error.message, 'error');
        }
    }

    /**
     * Confirm bulk delete
     */
    confirmBulkDelete() {
        const selectedCount = this.manager.selectedRows.size;

        if (selectedCount === 0) {
            showToast('Please select soldiers to delete', 'warning');
            return;
        }

        if (confirm(`Are you sure you want to delete ${selectedCount} selected soldier${selectedCount > 1 ? 's' : ''}? This action cannot be undone.`)) {
            this.bulkDelete();
        }
    }

    /**
     * Bulk delete selected soldiers - FIXED for 302 redirect
     */
    async bulkDelete() {
        const selectedIds = Array.from(this.manager.selectedRows);

        if (selectedIds.length === 0) {
            showToast('No soldiers selected', 'warning');
            return;
        }

        try {
            console.log('=== BULK DELETE REQUEST ===');
            console.log('Route:', routes.bulkDelete);
            console.log('Selected IDs:', selectedIds);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                throw new Error('CSRF token not found. Please refresh the page.');
            }
            console.log('CSRF Token:', csrfToken.substring(0, 20) + '...');

            const response = await fetch(routes.bulkDelete, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ ids: selectedIds }),
                credentials: 'same-origin', // Include cookies for session
                redirect: 'manual' // Don't follow redirects automatically
            });

            console.log('Response Status:', response.status);
            console.log('Response Type:', response.type);

            // Handle redirects (302)
            if (response.status === 302 || response.type === 'opaqueredirect') {
                console.error('❌ 302 Redirect detected');

                // Check if it's authentication redirect
                const redirectLocation = response.headers.get('Location');
                console.log('Redirect to:', redirectLocation);

                if (redirectLocation && redirectLocation.includes('login')) {
                    showToast('Session expired. Please log in again.', 'error');
                    setTimeout(() => {
                        window.location.href = redirectLocation;
                    }, 2000);
                } else {
                    throw new Error('Request was redirected (302). Possible CSRF token mismatch or authentication issue.');
                }
                return;
            }

            // Handle unauthorized (401)
            if (response.status === 401) {
                showToast('Unauthorized. Please log in.', 'error');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                return;
            }

            // Check if response is OK
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Server error response:', errorText);
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }

            // Check content type
            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);

            if (!contentType || !contentType.includes('application/json')) {
                const htmlResponse = await response.text();
                console.error('Expected JSON but got HTML:', htmlResponse.substring(0, 500));

                // Check if it's a login page
                if (htmlResponse.includes('login') || htmlResponse.includes('Login')) {
                    showToast('Session expired. Please log in again.', 'error');
                    setTimeout(() => window.location.reload(), 2000);
                    return;
                }

                throw new Error('Server returned HTML instead of JSON. Check Laravel logs for errors.');
            }

            const result = await response.json();
            console.log('Delete result:', result);
            console.log('======================');

            if (result.success) {
                showToast(`Successfully deleted ${selectedIds.length} soldier(s)`, 'success');
                this.manager.closeModal('bulk-modal');

                // Clear selections
                this.manager.selectedRows.clear();
                if (this.manager.elements.selectAll) {
                    this.manager.elements.selectAll.checked = false;
                    this.manager.elements.selectAll.indeterminate = false;
                }

                // Reload data
                await this.manager.loadData();
            } else {
                throw new Error(result.message || 'Failed to delete soldiers');
            }
        } catch (error) {
            console.error('Bulk delete error:', error);

            let errorMessage = 'Failed to delete soldiers';

            if (error.message.includes('CSRF token')) {
                errorMessage = 'Security token expired. Please refresh the page and try again.';
            } else if (error.message.includes('302') || error.message.includes('redirect')) {
                errorMessage = 'Request was redirected. This usually means authentication failed or CSRF token is invalid. Please refresh the page.';
            } else if (error.message.includes('HTML instead of JSON')) {
                errorMessage = 'Server error: The server returned an error page. Check browser console and Laravel logs.';
            } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                errorMessage = 'Network error: Please check your internet connection.';
            } else {
                errorMessage = error.message;
            }

            showToast(errorMessage, 'error');
        }
    }

    /**
     * Delete single profile
     */
    async deleteProfile(soldierId) {
        if (!confirm('Are you sure you want to delete this soldier profile?')) {
            return;
        }

        try {
            const response = await fetch(routes.delete.replace(':id', soldierId), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.success) {
                showToast('Soldier profile deleted successfully', 'success');
                await this.manager.loadData();
            } else {
                throw new Error(result.message || 'Failed to delete profile');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showToast('Failed to delete profile: ' + error.message, 'error');
        }
    }
}

// Make bulkActions globally accessible for onclick handlers
let bulkActions;
document.addEventListener('DOMContentLoaded', () => {
    // This will be set when SoldierProfileManager initializes
    bulkActions = window.manager?.bulkActions;
});
