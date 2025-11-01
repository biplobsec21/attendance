/**
 * FIXED: soldierExportActions.js
 * Export and bulk action functionality
 */

import { showToast } from "./soldierHelpers.js";

export function initExportAndBulkActions(manager) {
    // Excel Export
    const excelBtn = document.getElementById('export-excel');
    if (excelBtn) {
        excelBtn.addEventListener('click', () => {
            exportToExcel(manager);
        });
    }

    // PDF Export
    const pdfBtn = document.getElementById('export-pdf');
    if (pdfBtn) {
        pdfBtn.addEventListener('click', () => {
            exportToPDF(manager);
        });
    }

    // FIXED: Bulk Action Button - Use correct method name
    const bulkActionBtn = document.getElementById('bulk-action');
    if (bulkActionBtn) {
        bulkActionBtn.addEventListener('click', () => {
            // Use the correct method name from SoldierBulkActions class
            if (manager.bulkActions && typeof manager.bulkActions.showBulkActionModal === 'function') {
                manager.bulkActions.showBulkActionModal();
            } else {
                console.error('bulkActions.showBulkActionModal is not available');
                showToast('Bulk actions not available', 'error');
            }
        });
    }

    // Make manager globally accessible for bulk actions
    window.manager = manager;

    // Make bulkActions globally accessible for onclick handlers
    if (manager.bulkActions) {
        window.bulkActions = manager.bulkActions;
    }
}

/**
 * Export soldiers to Excel
 */
function exportToExcel(manager) {
    const soldiers = manager.filteredSoldiers.length > 0
        ? manager.filteredSoldiers
        : manager.soldiers;

    if (soldiers.length === 0) {
        showToast('No data to export', 'warning');
        return;
    }

    showToast(`Preparing to export ${soldiers.length} soldiers to Excel...`, 'info');

    try {
        // Prepare data for export
        const exportData = soldiers.map(soldier => ({
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

        // Create worksheet
        const ws = XLSX.utils.json_to_sheet(exportData);

        // Set column widths
        const colWidths = [
            { wch: 12 }, // Army No
            { wch: 25 }, // Name
            { wch: 12 }, // Rank
            { wch: 10 }, // Company
            { wch: 15 }, // Mobile
            { wch: 12 }, // Blood Group
            { wch: 15 }, // District
            { wch: 30 }, // Skills
            { wch: 30 }, // Courses
            { wch: 30 }, // Cadres
            { wch: 20 }, // ATT
            { wch: 25 }, // Education
            { wch: 20 }, // ERE
            { wch: 20 }, // CMD
            { wch: 25 }  // Ex-Areas
        ];
        ws['!cols'] = colWidths;

        // Create workbook
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Soldiers');

        // Generate filename with timestamp
        const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
        const filename = `soldiers_export_${timestamp}.xlsx`;

        // Download file
        XLSX.writeFile(wb, filename);

        showToast(`Successfully exported ${soldiers.length} soldiers to Excel`, 'success');
    } catch (error) {
        console.error('Excel export error:', error);
        showToast('Failed to export to Excel: ' + error.message, 'error');
    }
}

/**
 * Export soldiers to PDF
 */
function exportToPDF(manager) {
    const soldiers = manager.filteredSoldiers.length > 0
        ? manager.filteredSoldiers
        : manager.soldiers;

    if (soldiers.length === 0) {
        showToast('No data to export', 'warning');
        return;
    }

    showToast(`Preparing to export ${soldiers.length} soldiers to PDF...`, 'info');

    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'landscape',
            unit: 'mm',
            format: 'a4'
        });

        // Title
        doc.setFontSize(18);
        doc.setFont('helvetica', 'bold');
        doc.text('Soldier Profiles Export', 15, 15);

        // Subtitle with timestamp
        doc.setFontSize(10);
        doc.setFont('helvetica', 'normal');
        const exportDate = new Date().toLocaleString();
        doc.text(`Generated: ${exportDate} | Total Records: ${soldiers.length}`, 15, 22);

        // Prepare table data
        const tableData = soldiers.map(soldier => [
            soldier.army_no || '',
            soldier.name || '',
            soldier.rank || '',
            soldier.unit || '',
            soldier.mobile || '',
            soldier.blood_group || '',
            soldier.cocurricular?.map(s => s.name).join(', ').substring(0, 20) || '',
            soldier.courses?.map(c => c.name).join(', ').substring(0, 20) || ''
        ]);

        // Add table
        doc.autoTable({
            startY: 28,
            head: [['Army No', 'Name', 'Rank', 'Company', 'Mobile', 'Blood', 'Skills', 'Courses']],
            body: tableData,
            styles: {
                fontSize: 8,
                cellPadding: 2
            },
            headStyles: {
                fillColor: [34, 197, 94], // Green color
                textColor: 255,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [240, 240, 240]
            },
            columnStyles: {
                0: { cellWidth: 20 }, // Army No
                1: { cellWidth: 40 }, // Name
                2: { cellWidth: 20 }, // Rank
                3: { cellWidth: 15 }, // Company
                4: { cellWidth: 25 }, // Mobile
                5: { cellWidth: 15 }, // Blood
                6: { cellWidth: 40 }, // Skills
                7: { cellWidth: 40 }  // Courses
            },
            margin: { top: 28, left: 15, right: 15 },
            didDrawPage: function (data) {
                // Footer
                const pageCount = doc.internal.getNumberOfPages();
                doc.setFontSize(8);
                doc.text(
                    `Page ${data.pageNumber} of ${pageCount}`,
                    doc.internal.pageSize.width / 2,
                    doc.internal.pageSize.height - 10,
                    { align: 'center' }
                );
            }
        });

        // Generate filename with timestamp
        const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
        const filename = `soldiers_export_${timestamp}.pdf`;

        // Download file
        doc.save(filename);

        showToast(`Successfully exported ${soldiers.length} soldiers to PDF`, 'success');
    } catch (error) {
        console.error('PDF export error:', error);
        showToast('Failed to export to PDF: ' + error.message, 'error');
    }
}

/**
 * Export selected soldiers only
 */
export function exportSelectedToExcel(manager) {
    const selectedSoldiers = manager.getSelectedSoldiers();

    if (selectedSoldiers.length === 0) {
        showToast('No soldiers selected', 'warning');
        return;
    }

    showToast(`Exporting ${selectedSoldiers.length} selected soldiers to Excel...`, 'info');

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
    } catch (error) {
        console.error('Excel export error:', error);
        showToast('Failed to export: ' + error.message, 'error');
    }
}

/**
 * Export selected soldiers to PDF
 */
export function exportSelectedToPDF(manager) {
    const selectedSoldiers = manager.getSelectedSoldiers();

    if (selectedSoldiers.length === 0) {
        showToast('No soldiers selected', 'warning');
        return;
    }

    showToast(`Exporting ${selectedSoldiers.length} selected soldiers to PDF...`, 'info');

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
        doc.text(`Generated: ${exportDate} | Selected Records: ${selectedSoldiers.length}`, 15, 22);

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
                0: { cellWidth: 20 },
                1: { cellWidth: 40 },
                2: { cellWidth: 20 },
                3: { cellWidth: 15 },
                4: { cellWidth: 25 },
                5: { cellWidth: 15 },
                6: { cellWidth: 40 },
                7: { cellWidth: 40 }
            },
            margin: { top: 28, left: 15, right: 15 }
        });

        const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
        const filename = `soldiers_selected_${timestamp}.pdf`;

        doc.save(filename);

        showToast(`Successfully exported ${selectedSoldiers.length} selected soldiers`, 'success');
    } catch (error) {
        console.error('PDF export error:', error);
        showToast('Failed to export: ' + error.message, 'error');
    }
}
