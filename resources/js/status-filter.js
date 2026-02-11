export function createStatusFilter(config) {
    return function(status) {
        if (config.currentFilterVar) {
            window[config.currentFilterVar] = status;
        }
        
        if (config.activeFiltersVar) {
            if (!window[config.activeFiltersVar]) {
                window[config.activeFiltersVar] = {};
            }
            window[config.activeFiltersVar].status = status;
        }

        document.querySelectorAll('.stats-card').forEach(card => {
            card.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2');
        });
        
        // Add ring styling to selected card
        const selectedCard = document.querySelector(`[data-filter="${status}"]`);
        if (selectedCard) {
            selectedCard.classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');
        }

        // Get the table instance from config
        const tableVar = window[config.tableVar];

        // Check if table is initialized
        if (!tableVar) {
            console.warn(`Table '${config.tableVar}' not yet initialized. Skipping filter.`);
            return;
        }

        // Check if this is a DataTableManager instance with batch loading
        if (tableVar.batchLoading !== undefined && tableVar.batchLoading) {
            console.log('Batch loading detected (DataTableManager), applying status filter:', status);
            tableVar.applyStatusFilter(status);
        } else if (tableVar.init && typeof tableVar.init === 'function') {
            // This is a DataTableManager without batch loading or a native DataTable
            const dt = tableVar.table || tableVar;
            if (dt.ajax && dt.ajax.url) {
                // Server-side processing
                console.log('Server-side processing detected, sending status parameter:', status);
                const currentUrl = dt.ajax.url();
                const url = new URL(currentUrl, window.location.origin);
                url.searchParams.set('status', status);
                dt.ajax.url(url.toString()).load();
            } else {
                // Client-side processing on native DataTable
                const columnIndex = config.columnIndex || 7;
                console.log('Client-side processing, searching in column', columnIndex, 'for:', status);
                
                if (status === 'all') {
                    dt.column(columnIndex).search('').draw();
                } else {
                    dt.column(columnIndex).search(status).draw();
                }
            }
        } else {
            // This is a native DataTable instance
            const columnIndex = config.columnIndex || 7;
            console.log('Native DataTable, searching in column', columnIndex, 'for:', status);
            
            if (status === 'all') {
                tableVar.column(columnIndex).search('').draw();
            } else {
                tableVar.column(columnIndex).search(status).draw();
            }
        }

        console.log(`Status filter applied: ${status}`);
    };
}