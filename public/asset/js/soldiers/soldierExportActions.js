// public/js/soldiers/soldierExportActions.js

export function initExportAndBulkActions(manager) {
    // Excel Export
    document.getElementById("export-excel").addEventListener("click", () => {
        handleExport(manager, "excel");
    });

    // PDF Export
    document.getElementById("export-pdf").addEventListener("click", () => {
        handleExport(manager, "pdf");
    });

    // Bulk Action Button
    document.getElementById("bulk-action").addEventListener("click", () => {
        manager.bulkActions.showBulkActions();
    });
}

function handleExport(manager, format) {
    if (manager.selectedRows.size === 0) {
        if (!confirm("No soldiers selected. Do you want to export all filtered soldiers?")) {
            return;
        }
    }

    const params = new URLSearchParams({
        ...manager.filters,
        format: format,
        selected: Array.from(manager.selectedRows).join(","),
    });

    window.open(`/army/export?${params}`, "_blank");
}

function handleBulkAction(manager) {
    if (manager.selectedRows.size === 0) {
        alert("Please select at least one soldier for bulk actions.");
        return;
    }

    // Example: Show bulk delete confirmation
    if (confirm(`Perform bulk action on ${manager.selectedRows.size} selected soldiers?`)) {
        // TODO: Replace with API call
        alert("Bulk action executed!");
    }
}
