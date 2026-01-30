export class DataTableManager {
    constructor(config) {
        this.tableId = config.tableId;
        this.ajaxUrl = config.ajaxUrl;
        this.columns = config.columns;
        this.order = config.order || [[0, 'desc']];
        this.pageLength = config.pageLength || 10;
        this.responsive = config.responsive !== false;
        this.serverSide = config.serverSide || false;
        this.onInitComplete = config.onInitComplete || function() {};
        this.table = null;
    }

    init() {
        if ($.fn.DataTable.isDataTable(`#${this.tableId}`)) {
            $(`#${this.tableId}`).DataTable().destroy();
        }

        const config = {
            serverSide: this.serverSide,
            ajax: {
                url: this.ajaxUrl,
                dataSrc: this.serverSide ? 'data' : 'data',
                error: (xhr, error, thrown) => {
                    console.error('DataTables AJAX error:', error, thrown);
                    // Remove automatic retry to prevent infinite loops
                    // Only retry once for network errors
                    if (xhr.status === 0) {
                        console.warn('Network error detected, retrying once...');
                        setTimeout(() => this.table.ajax.reload(), 2000); // Increased delay
                    }
                }
            },
            columns: this.columns,
            order: this.order,
            pageLength: this.pageLength,
        };

        if (this.responsive) {
            config.responsive = {
                details: {
                    type: 'column',
                    target: 0
                }
            };
            config.columnDefs = [{
                className: 'dtr-control',
                orderable: false,
                targets: 0
            }];
        }

        if (this.onInitComplete) {
            config.initComplete = this.onInitComplete;
        }

        this.table = new DataTable(`#${this.tableId}`, config);
        return this.table;
    }

    reload(resetPaging = false) {
        if (this.table) {
            this.table.ajax.reload(null, resetPaging);
        }
    }

    destroy() {
        if (this.table) {
            this.table.destroy();
            this.table = null;
        }
    }
}