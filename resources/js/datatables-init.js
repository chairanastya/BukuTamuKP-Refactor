export class DataTableManager {
    constructor(config) {
        this.tableId = config.tableId;
        this.ajaxUrl = config.ajaxUrl;
        this.columns = config.columns;
        this.order = config.order || [[0, 'desc']];
        this.pageLength = config.pageLength || 10;
        this.responsive = config.responsive !== false;
        this.serverSide = config.serverSide || false;
        this.batchLoading = config.batchLoading || false;
        this.batchSize = config.batchSize || 100;
        this.onInitComplete = config.onInitComplete || function() {};
        this.table = null;
        this.allData = [];
        this.currentOffset = 0;
        this.totalRecords = 0;
        this.currentStatusFilter = 'all';
        this.isLoading = false;
        this.initialLoadDone = false;
        this.loadPromise = null; // Cache for the initial load promise
    }

    init() {
        console.log('[init] Initializing DataTable...');
        
        // Destroy existing table if it exists
        if ($.fn.DataTable.isDataTable(`#${this.tableId}`)) {
            console.log('[init] Destroying existing DataTable instance...');
            $(`#${this.tableId}`).DataTable().destroy();
        }

        // Reset state
        this.allData = [];
        this.currentOffset = 0;
        this.totalRecords = 0;
        this.initialLoadDone = false;
        this.loadPromise = null;

        const self = this;
        const config = {
            serverSide: false, // Always client-side for batch loading
            ajax: this.batchLoading ? this.loadBatchData.bind(this) : {
                url: this.ajaxUrl,
                dataSrc: 'data',
                error: (xhr, error, thrown) => {
                    console.error('DataTables AJAX error:', error, thrown);
                    if (xhr.status === 0) {
                        console.warn('Network error detected, retrying once...');
                        setTimeout(() => self.table.ajax.reload(), 2000);
                    }
                }
            },
            columns: this.columns,
            order: this.order,
            pageLength: this.pageLength,
            deferRender: true, // Important for performance with large datasets
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

        // For batch loading, add page change handler and load more button
        if (this.batchLoading) {
            // Remove auto-loading on page change, only load when explicitly requested
            config.drawCallback = function(settings) {
                // Update load more button visibility
                self.updateLoadMoreButton();
            };
        }

        this.table = new DataTable(`#${this.tableId}`, config);
        return this.table;
    }

    loadBatchData(data, callback, settings) {
        console.log(`[loadBatchData] Called - initialLoadDone: ${this.initialLoadDone}, allData.length: ${this.allData.length}`);
        
        // For initial load, load first batch only once
        if (!this.initialLoadDone) {
            this.initialLoadDone = true;
            
            // If already loading, reuse the same promise
            if (!this.loadPromise) {
                console.log('[loadBatchData] Starting initial batch load...');
                this.loadPromise = this.loadNextBatch();
            } else {
                console.log('[loadBatchData] Reusing existing load promise...');
            }
            
            this.loadPromise.then(() => {
                console.log(`[loadBatchData] Initial batch loaded, returning ${this.allData.length} records`);
                this.loadPromise = null; // Clear the cached promise
                callback({
                    data: this.allData,
                    recordsTotal: this.totalRecords,
                    recordsFiltered: this.totalRecords
                });
            }).catch(error => {
                console.error('[loadBatchData] Error loading initial batch:', error);
                this.loadPromise = null; // Clear the cached promise on error
                callback({
                    data: [],
                    recordsTotal: 0,
                    recordsFiltered: 0
                });
            });
        } else {
            // Data already loaded, just return current data
            console.log(`[loadBatchData] Returning existing data: ${this.allData.length} records`);
            callback({
                data: this.allData,
                recordsTotal: this.totalRecords,
                recordsFiltered: this.totalRecords
            });
        }
    }

    async loadNextBatch() {
        if (this.isLoading) {
            console.log('[loadNextBatch] Already loading, skipping...');
            return;
        }
        
        this.isLoading = true;
        console.log(`[loadNextBatch] Loading batch: offset=${this.currentOffset}, batch_size=${this.batchSize}, status=${this.currentStatusFilter}`);
        
        try {
            const response = await fetch(`${this.ajaxUrl}?offset=${this.currentOffset}&batch_size=${this.batchSize}&status=${this.currentStatusFilter}`);
            const result = await response.json();
            
            console.log(`[loadNextBatch] Received ${result.data?.length || 0} records from server`);
            
            // Only add new data, don't duplicate
            if (result.data && result.data.length > 0) {
                const beforeLength = this.allData.length;
                this.allData = this.allData.concat(result.data);
                this.totalRecords = result.total;
                this.currentOffset += result.data.length; // Increment by actual data received, not batch size
                
                console.log(`[loadNextBatch] Added ${result.data.length} records. Before: ${beforeLength}, After: ${this.allData.length}, Total: ${this.totalRecords}`);
                
                // ONLY update table if this is NOT the initial load
                // Initial load will be rendered by the callback in loadBatchData
                if (this.table && beforeLength > 0) {
                    console.log('[loadNextBatch] Updating table with new data (Load More)');
                    this.table.clear();
                    this.table.rows.add(this.allData);
                    this.table.draw(false);
                } else {
                    console.log('[loadNextBatch] Skipping table update (initial load, callback will handle)');
                }
                
                // Refresh filter options jika multiFilter sudah ada
                if (window.multiFilter && typeof window.multiFilter.refreshFilterOptions === 'function') {
                    console.log('[loadNextBatch] Refreshing filter options after data load');
                    window.multiFilter.refreshFilterOptions();
                }
            } else {
                console.log('[loadNextBatch] No more data to load');
            }
            
            // Update load more button
            this.updateLoadMoreButton();
        } catch (error) {
            console.error('[loadNextBatch] Error loading batch data:', error);
            // Reset button on error
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            if (loadMoreBtn) {
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Load More Records';
            }
        } finally {
            this.isLoading = false;
        }
    }

    // Method to apply status filter
    applyStatusFilter(status) {
        console.log(`[applyStatusFilter] Applying filter: ${status}`);
        this.currentStatusFilter = status;
        this.allData = [];  // CLEAR all data when filter changes
        this.currentOffset = 0;
        this.totalRecords = 0;
        this.initialLoadDone = false; // Reset for new filter
        this.loadPromise = null; // Clear cached promise
        
        if (this.table) {
            this.table.clear();
            this.table.draw(); // Clear the table display first
            this.table.ajax.reload(); // This will trigger loadBatchData again
        }
    }

    // Load more data (called by load more button)
    loadMore() {
        if (!this.isLoading && this.allData.length < this.totalRecords) {
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            if (loadMoreBtn) {
                loadMoreBtn.disabled = true;
                const remaining = Math.min(this.batchSize, this.totalRecords - this.allData.length);
                loadMoreBtn.textContent = `Loading ${remaining} more...`;
            }
            return this.loadNextBatch();
        } else if (this.allData.length >= this.totalRecords) {
            console.log('All data already loaded');
        }
    }

    // Update load more button visibility
    updateLoadMoreButton() {
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (loadMoreBtn) {
            loadMoreBtn.disabled = false;
            
            // Show button only if there's more data to load
            if (this.totalRecords > 0 && this.allData.length < this.totalRecords) {
                loadMoreBtn.style.display = 'block';
                const remaining = Math.min(this.batchSize, this.totalRecords - this.allData.length);
                loadMoreBtn.textContent = `Load ${remaining} More Records (${this.allData.length}/${this.totalRecords})`;
            } else {
                loadMoreBtn.style.display = 'none';
                loadMoreBtn.textContent = 'Load More Records';
            }
        }
    }

    reload(resetPaging = false) {
        if (this.batchLoading) {
            console.log('[reload] Reloading batch data...');
            // Reset state completely
            this.allData = [];
            this.currentOffset = 0;
            this.totalRecords = 0;
            this.initialLoadDone = false;
            this.loadPromise = null; // Clear cached promise
            
            // Reload via ajax which will call loadBatchData
            if (this.table) {
                this.table.ajax.reload(null, resetPaging);
            }
        } else if (this.table) {
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