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
        const columnIndex = config.columnIndex || 7;

        // Check if table is initialized
        if (!tableVar) {
            console.warn(`Table '${config.tableVar}' not yet initialized. Skipping filter.`);
            return;
        }

        // Check if column method exists
        if (typeof tableVar.column !== 'function') {
            console.warn(`Table instance doesn't have 'column' method. Table might not be fully initialized.`);
            return;
        }

        if (status === 'all') {
            tableVar.column(columnIndex).search('').draw();
        } else {
            if (config.useRegex) {
                const searchPattern = `^${status}$`;
                tableVar.column(columnIndex).search(searchPattern, true, false).draw();
            } else {
                tableVar.column(columnIndex).search(status).draw();
            }
        }

        console.log(`Status filter applied: ${status}, Column: ${columnIndex}`);
    };
}