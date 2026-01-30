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

        // Check if server-side processing
        if (tableVar.init && tableVar.init().serverSide) {
            console.log('Server-side processing detected, sending status parameter:', status);
            // For server-side, we need to modify ajax params and reload
            // DataTables doesn't have a direct way to add custom params, so we use ajax.url with query string
            const currentUrl = tableVar.ajax.url();
            const url = new URL(currentUrl, window.location.origin);
            url.searchParams.set('status', status);
            tableVar.ajax.url(url.toString()).load();
        } else {
            // For client-side processing, use column search
            const columnIndex = config.columnIndex || 7;
            console.log('Client-side processing, searching in column', columnIndex, 'for:', status);
            
            if (status === 'all') {
                tableVar.column(columnIndex).search('').draw();
            } else {
                tableVar.column(columnIndex).search(status).draw();
            }
        }

        console.log(`Status filter applied: ${status}`);
    };
}