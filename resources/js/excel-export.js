export class ExcelExporter {
    constructor(config) {
        this.table = config.table;
        this.activeFilters = config.activeFilters;
        this.title = config.title || 'LAPORAN KUNJUNGAN';
        this.sheetName = config.sheetName || 'Kunjungan';
        this.filePrefix = config.filePrefix || 'Laporan_Kunjungan';
        this.useMonthYear = config.useMonthYear || false;
        this.monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    }

    export() {
        // Ambil data yang sedang ditampilkan (setelah filter)
        const filteredData = this.table.rows({ search: 'applied' }).data().toArray();

        if (filteredData.length === 0) {
            alert('Tidak ada data untuk diekspor');
            return;
        }

        // Generate tanggal untuk header dan filename
        const today = new Date();
        const dateStr = today.toISOString().split('T')[0];
        const monthYear = `${this.monthNames[today.getMonth()]} ${today.getFullYear()}`;
        const periodeValue = this.useMonthYear ? monthYear : dateStr;

        // Build filter description
        const filterDescription = this._buildFilterDescription();
        const periodeText = `Periode: ${periodeValue} | Filter: ${filterDescription}`;

        // Siapkan data untuk Excel
        const excelData = this._prepareData(filteredData);

        // Buat workbook dan worksheet
        const wb = XLSX.utils.book_new();
        const ws = this._createWorksheet(excelData, periodeText);

        // Add to workbook
        XLSX.utils.book_append_sheet(wb, ws, this.sheetName);

        // Download file dengan nama yang deskriptif
        const filename = this._generateFilename(dateStr);
        XLSX.writeFile(wb, filename);
    }

    _buildFilterDescription() {
        const filterParts = [];

        // Status filter
        if (this.activeFilters.status !== 'all') {
            filterParts.push(`Status: ${this.activeFilters.status.charAt(0).toUpperCase() + this.activeFilters.status.slice(1)}`);
        }

        // Instansi filter
        if (this.activeFilters.instansi) {
            filterParts.push(`Instansi: ${this.activeFilters.instansi}`);
        }

        // Karyawan filter
        if (this.activeFilters.karyawan) {
            filterParts.push(`Karyawan: ${this.activeFilters.karyawan}`);
        }

        // Tanggal filter
        if (this.activeFilters.tanggal) {
            filterParts.push(`Tanggal: ${this.activeFilters.tanggal}`);
        }

        return filterParts.length > 0 ? filterParts.join(' | ') : 'Semua Data';
    }

    _prepareData(filteredData) {
        return filteredData.map((row, index) => {
            let karyawanNama = '-';
            let karyawanJabatan = '-';
            let karyawanDepartemen = '-';

            if (row.karyawan && row.karyawan.length > 0) {
                karyawanNama = row.karyawan.map(k => k.nama).join(', ');
                karyawanJabatan = row.karyawan.map(k => k.jabatan).join(', ');
                karyawanDepartemen = row.karyawan.map(k => k.departemen).join(', ');
            }

            return [
                index + 1,
                row.tanggal,
                row.jam,
                row.nama_tamu,
                row.email_tamu,
                row.instansi,
                row.tujuan_kunjungan,
                karyawanNama,
                karyawanJabatan,
                karyawanDepartemen,
                row.status.toUpperCase(),
                row.alasan_batal || '-'
            ];
        });
    }

    _createWorksheet(excelData, periodeText) {
        const ws = {};

        // Title dan Periode
        ws['A1'] = { v: this.title, t: 's' };
        ws['A2'] = { v: periodeText, t: 's' };

        // Header kolom
        const headers = ['No', 'Tanggal', 'Jam', 'Nama Tamu', 'Email Tamu', 'Instansi', 'Tujuan Kunjungan', 'PIC Karyawan', 'Jabatan PIC', 'Departemen PIC', 'Status', 'Alasan Batal'];
        headers.forEach((header, idx) => {
            const cellRef = XLSX.utils.encode_cell({ r: 3, c: idx });
            ws[cellRef] = { v: header, t: 's' };
        });

        // Data rows
        excelData.forEach((row, rowIdx) => {
            row.forEach((cell, colIdx) => {
                const cellRef = XLSX.utils.encode_cell({ r: rowIdx + 4, c: colIdx });
                ws[cellRef] = { v: cell, t: typeof cell === 'number' ? 'n' : 's' };
            });
        });

        // Total row
        const totalRow = excelData.length + 4;
        ws[XLSX.utils.encode_cell({ r: totalRow, c: 0 })] = { v: 'TOTAL', t: 's' };
        ws[XLSX.utils.encode_cell({ r: totalRow, c: 1 })] = { v: `${excelData.length} Kunjungan`, t: 's' };

        // Set range
        const range = { s: { r: 0, c: 0 }, e: { r: totalRow, c: 11 } };
        ws['!ref'] = XLSX.utils.encode_range(range);

        // Column widths
        ws['!cols'] = [
            { wch: 5 }, { wch: 12 }, { wch: 10 }, { wch: 20 }, { wch: 25 }, { wch: 20 },
            { wch: 30 }, { wch: 20 }, { wch: 20 }, { wch: 20 }, { wch: 12 }, { wch: 30 }
        ];

        // Apply styles
        this._applyStyles(ws, headers, excelData, totalRow);

        return ws;
    }

    _applyStyles(ws, headers, excelData, totalRow) {
        // Styling constants
        const headerFill = { patternType: 'solid', fgColor: { rgb: '4472C4' } };
        const headerFont = { bold: true, color: { rgb: 'FFFFFF' } };
        const totalFill = { patternType: 'solid', fgColor: { rgb: 'FFEB9C' } };
        const totalFont = { bold: true };
        const border = {
            top: { style: 'thin', color: { rgb: '000000' } },
            bottom: { style: 'thin', color: { rgb: '000000' } },
            left: { style: 'thin', color: { rgb: '000000' } },
            right: { style: 'thin', color: { rgb: '000000' } }
        };

        // Apply styles to title
        ws['A1'].s = { font: { bold: true, sz: 16 }, alignment: { horizontal: 'center', vertical: 'center' } };
        ws['A2'].s = { font: { italic: true, sz: 11 }, alignment: { horizontal: 'center' } };

        // Merge title cells
        ws['!merges'] = [
            { s: { r: 0, c: 0 }, e: { r: 0, c: 11 } },
            { s: { r: 1, c: 0 }, e: { r: 1, c: 11 } },
            { s: { r: totalRow, c: 1 }, e: { r: totalRow, c: 11 } }
        ];

        // Apply styles to headers
        headers.forEach((_, idx) => {
            const cellRef = XLSX.utils.encode_cell({ r: 3, c: idx });
            ws[cellRef].s = {
                fill: headerFill,
                font: headerFont,
                alignment: { horizontal: 'center', vertical: 'center' },
                border: border
            };
        });

        // Apply borders to data cells
        excelData.forEach((row, rowIdx) => {
            row.forEach((_, colIdx) => {
                const cellRef = XLSX.utils.encode_cell({ r: rowIdx + 4, c: colIdx });
                if (ws[cellRef]) {
                    ws[cellRef].s = {
                        border: border,
                        alignment: { vertical: 'center', wrapText: colIdx >= 6 }
                    };
                }
            });
        });

        // Apply styles to total row
        ws[XLSX.utils.encode_cell({ r: totalRow, c: 0 })].s = {
            fill: totalFill,
            font: totalFont,
            alignment: { horizontal: 'center', vertical: 'center' },
            border: border
        };
        ws[XLSX.utils.encode_cell({ r: totalRow, c: 1 })].s = {
            fill: totalFill,
            font: totalFont,
            alignment: { horizontal: 'left', vertical: 'center' },
            border: border
        };

        // Row heights
        ws['!rows'] = [
            { hpt: 24 }, // Title
            { hpt: 18 }, // Periode
            { hpt: 6 },  // Empty row
            { hpt: 30 }  // Header
        ];
    }

    _generateFilename(dateStr) {
        const filenameParts = [this.filePrefix, dateStr];
        
        if (this.activeFilters.status !== 'all') {
            filenameParts.push(this.activeFilters.status);
        }
        
        if (this.activeFilters.instansi) {
            filenameParts.push(this.activeFilters.instansi.replace(/\s+/g, '_'));
        }
        
        if (this.activeFilters.karyawan) {
            filenameParts.push(this.activeFilters.karyawan.replace(/\s+/g, '_'));
        }
        
        return filenameParts.join('_') + '.xlsx';
    }
}