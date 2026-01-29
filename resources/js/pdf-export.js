export function exportDataTablePDF(options = {}) {
    const {
        title = 'Laporan',
        subtitle = '',
        filename = 'export',
        columns = [],
        data = [],
        dataMapper = null,
        filterInfo = '',
        footerText = 'Items',
        buttonId = null
    } = options;

    console.log('exportDataTablePDF called with:', {
        title,
        subtitle,
        filename,
        columnsLength: columns.length,
        dataLength: data.length,
        dataMapperExists: !!dataMapper,
        filterInfo,
        footerText
    });

    if (data && data.length > 0) {
        console.log('First data row:', data[0]);
        console.log('Columns structure:', columns);
    }

    if (buttonId) {
        const btn = document.getElementById(buttonId);
        if (btn) btn.disabled = true;
    }

    try {
        if (!window.jspdf) {
            throw new Error('jsPDF not loaded');
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4'); // landscape

        // Title
        doc.setFontSize(18);
        doc.setFont('helvetica', 'bold');
        doc.text(title, doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });

        // Subtitle with filter info
        if (subtitle || filterInfo) {
            doc.setFontSize(10);
            doc.setFont('helvetica', 'italic');
            const subtitleText = subtitle || filterInfo;
            doc.text(subtitleText, doc.internal.pageSize.getWidth() / 2, 22, { align: 'center' });
        }

        // Transform data if dataMapper provided
        let tableData = data;
        if (dataMapper && typeof dataMapper === 'function') {
            console.log('Using dataMapper to transform data');
            tableData = data.map((row, index) => dataMapper(row, index));
        } else if (Array.isArray(data) && data.length > 0 && typeof data[0] === 'object' && !Array.isArray(data[0])) {
            // If data is array of objects, extract values based on columns
            console.log('Data is array of objects, extracting...');
            tableData = data.map(row => {
                return columns.map(col => row[col.key] || '-');
            });
        } else {
            console.log('Data is array of arrays, using as-is');
            tableData = data;
        }

        console.log('TableData prepared, rows:', tableData.length);
        if (tableData.length > 0) {
            console.log('First tableData row:', tableData[0]);
        }

        // Prepare headers
        const headers = Array.isArray(columns[0]) 
            ? columns 
            : [columns.map(col => col.label || col)];

        console.log('Headers:', headers);

        // Generate table
        doc.autoTable({
            startY: subtitle || filterInfo ? 28 : 25,
            head: headers,
            body: tableData,
            theme: 'grid',
            headStyles: {
                fillColor: [68, 114, 196],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'center',
                valign: 'middle',
                fontSize: 9,
                cellPadding: 3
            },
            bodyStyles: {
                fontSize: 8,
                valign: 'middle',
                cellPadding: 3,
                minCellHeight: 10
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            styles: {
                lineColor: [0, 0, 0],
                lineWidth: 0.1,
                overflow: 'linebreak',
                cellWidth: 'wrap'
            },
            margin: { top: 28, left: 10, right: 10, bottom: 15 },
            tableWidth: 'auto'
        });

        // Footer with total
        const finalY = doc.lastAutoTable.finalY || 28;
        const pageWidth = doc.internal.pageSize.getWidth();
        const tableWidth = 277; // Default total column widths
        const startX = (pageWidth - tableWidth) / 2;

        doc.setFillColor(255, 235, 156);
        doc.rect(startX, finalY + 2, tableWidth, 10, 'F');
        doc.setDrawColor(0, 0, 0);
        doc.rect(startX, finalY + 2, tableWidth, 10, 'S');
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(0, 0, 0);
        doc.text(`TOTAL: ${data.length} ${footerText}`, startX + 4, finalY + 8);

        doc.save(filename + '.pdf');
        console.log('PDF saved successfully');
    } catch (error) {
        console.error('PDF export error:', error);
        console.error('Error stack:', error.stack);
        alert('Terjadi kesalahan saat membuat PDF: ' + error.message);
    } finally {
        if (buttonId) {
            const btn = document.getElementById(buttonId);
            if (btn) btn.disabled = false;
        }
    }
}

export async function exportContentPDF(options = {}) {
    const {
        title = 'Laporan',
        filename = 'export',
        contentElement = null,
        buttonId = null
    } = options;

    if (buttonId) {
        const btn = document.getElementById(buttonId);
        if (btn) btn.disabled = true;
    }

    try {
        if (!window.jspdf || !window.html2canvas) {
            throw new Error('Required libraries not loaded');
        }

        const { jsPDF } = window.jspdf;
        const element = document.getElementById(contentElement) || contentElement;

        if (!element) {
            throw new Error('Content element not found');
        }

        const canvas = await html2canvas(element, {
            scale: 2,
            useCORS: true,
            logging: false,
            allowTaint: true
        });

        const imgData = canvas.toDataURL('image/png');
        const doc = new jsPDF('p', 'mm', 'a4');
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();

        let imgWidth = pageWidth - 20;
        let imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;
        let position = 10;

        const imgSrc = imgData;
        doc.addImage(imgSrc, 'PNG', 10, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
            position = heightLeft - imgHeight + 10;
            doc.addPage();
            doc.addImage(imgSrc, 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        doc.save(filename + '.pdf');
    } catch (error) {
        console.error('PDF export error:', error);
        alert('Terjadi kesalahan saat membuat PDF');
    } finally {
        if (buttonId) {
            const btn = document.getElementById(buttonId);
            if (btn) btn.disabled = false;
        }
    }
}

export default {
    exportDataTablePDF,
    exportContentPDF
};