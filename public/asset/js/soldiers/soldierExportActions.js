// public/js/soldiers/soldierExportActions.js

export function initExportAndBulkActions(manager) {
    console.log('Initializing export and bulk actions...');

    // Wait for DOM to be ready
    setTimeout(() => {
        // Excel Export
        const excelBtn = document.getElementById("export-excel");
        if (excelBtn) {
            excelBtn.addEventListener("click", () => {
                handleExport(manager, "excel");
            });
            console.log('✅ Excel export button initialized');
        } else {
            console.warn('❌ Excel export button not found');
        }

        // PDF Export
        const pdfBtn = document.getElementById("export-pdf");
        if (pdfBtn) {
            pdfBtn.addEventListener("click", () => {
                handleExport(manager, "pdf");
            });
            console.log('✅ PDF export button initialized');
        } else {
            console.warn('❌ PDF export button not found');
        }

        // Bulk Action Button
        const bulkBtn = document.getElementById("bulk-action");
        if (bulkBtn) {
            bulkBtn.addEventListener("click", () => {
                if (manager.bulkActions) {
                    manager.bulkActions.showBulkActions();
                } else {
                    console.error('Bulk actions not initialized');
                }
            });
            console.log('✅ Bulk action button initialized');
        } else {
            console.warn('❌ Bulk action button not found');
        }

    }, 100);
}

function handleExport(manager, format) {
    console.log(`Exporting in ${format} format...`);

    // Build export parameters
    const params = new URLSearchParams({
        format: format
    });

    // Add filters
    Object.entries(manager.filters).forEach(([key, value]) => {
        if (value && value !== '' && (!Array.isArray(value) || value.length > 0)) {
            if (Array.isArray(value)) {
                value.forEach(v => params.append(`${key}[]`, v));
            } else {
                params.append(key, value);
            }
        }
    });

    // Add selected rows if any
    if (manager.selectedRows.size > 0) {
        params.append('selected', Array.from(manager.selectedRows).join(","));
        console.log(`Exporting ${manager.selectedRows.size} selected soldiers`);
    } else {
        console.log('Exporting all filtered soldiers');
    }

    const exportUrl = `/soldiers/export?${params}`;
    console.log('Export URL:', exportUrl);

    window.open(exportUrl, "_blank");
}

// Optional: Add keyboard shortcuts
document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        const excelBtn = document.getElementById('export-excel');
        if (excelBtn) excelBtn.click();
    }
});
