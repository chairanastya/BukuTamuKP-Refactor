export function createStatusFilter(config) {
    return function(status) {
        if (config.currentFilterVar) {
            window[config.currentFilterVar] = status;
        }
        
        if (config.activeFiltersVar) {
            window[config.activeFiltersVar].status = status;
        }

        document.querySelectorAll('.stats-card').forEach(card => {
            card.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2');
        });
        
        const selector = config.multipleCards ? 
            `[data-filter="${status}"]` : 
            `[data-filter="${status}"]`;
        
        if (config.multipleCards) {
            document.querySelectorAll(selector).forEach(card => {
                card.classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');
            });
        } else {
            document.querySelector(selector).classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');
        }

        const tableVar = window[config.tableVar];
        const columnIndex = config.columnIndex;

        if (status === 'all') {
            tableVar.column(columnIndex).search('').draw();
        } else if (config.useRegex) {
            const searchTerm = status === 'aktif' ? '^Aktif$' : '^Nonaktif$';
            tableVar.column(columnIndex).search(searchTerm, true, false).draw();
        } else {
            tableVar.column(columnIndex).search(status).draw();
        }
    };
}