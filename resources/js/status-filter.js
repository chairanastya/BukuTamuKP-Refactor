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

        // Prefer DataTableManager batch-loading path when available
        if (tableVar.applyStatusFilter && tableVar.batchLoading) {
            console.log('Batch loading detected (DataTableManager), applying status filter:', status);
            tableVar.applyStatusFilter(status);
            console.log(`Status filter applied via DataTableManager: ${status}`);
            return;
        }

        // Resolve underlying DataTable instance (if wrapped)
        const dt = (tableVar.table || tableVar);

        // If config forces client-side filtering, or DataTableManager without batch-loading, use column search
        const columnIndex = config.columnIndex || 7;

        // Determine if we should perform client-side column search
        const forceClient = config.forceClientSide === true;
        const isManagerNoBatch = tableVar.applyStatusFilter && !tableVar.batchLoading;

        if (forceClient || isManagerNoBatch) {
            try {
                console.log('Applying client-side column search on column', columnIndex, 'for:', status);
                if (status === 'all') {
                    dt.column(columnIndex).search('').draw();
                } else {
                    dt.column(columnIndex).search(status).draw();
                }
                console.log(`Status filter applied (client-side): ${status}`);
                return;
            } catch (e) {
                console.warn('Client-side filtering failed, falling back to AJAX URL approach', e);
            }
        }

        // Fallback: attempt to treat as server-side and pass status param via AJAX URL
        try {
            const isServerSide = dt && dt.settings && dt.settings()[0] && dt.settings()[0].oFeatures && dt.settings()[0].oFeatures.bServerSide;
            if (!isServerSide && dt && dt.column) {
                // DataTable is client-side even if it uses AJAX - use column search
                console.log('Detected client-side DataTable (bServerSide=false), applying client-side column search on column', columnIndex, 'for:', status);
                if (status === 'all') {
                    dt.column(columnIndex).search('').draw();
                } else {
                    dt.column(columnIndex).search(status).draw();
                }
                console.log(`Status filter applied (client-side via settings): ${status}`);
                return;
            }

            if (isServerSide && dt && dt.ajax && dt.ajax.url) {
                console.log('Server-side processing detected, sending status parameter:', status);
                const currentUrl = dt.ajax.url();
                const url = new URL(currentUrl, window.location.origin);
                url.searchParams.set('status', status);
                dt.ajax.url(url.toString()).load();
                console.log(`Status filter applied via AJAX URL: ${status}`);
                return;
            }
        } catch (e) {
            console.warn('Server-side URL update attempt failed', e);
        }

        // As a last resort attempt native column search if dt refers to DataTable instance
        try {
            console.log('Native DataTable fallback, searching in column', columnIndex, 'for:', status);
            if (status === 'all') {
                tableVar.column(columnIndex).search('').draw();
            } else {
                tableVar.column(columnIndex).search(status).draw();
            }
            console.log(`Status filter applied (native fallback): ${status}`);
            return;
        } catch (e) {
            console.error('Unable to apply status filter on any known path', e);
        }

        console.log(`Status filter applied: ${status}`);
    };
}