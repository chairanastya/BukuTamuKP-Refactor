export function exportDataTablePDF(options = {}) {
    const {
        title = 'Laporan',
        subtitle = '',
        filename = 'export',
        columns = [],
        data = [],
        filters = {},
        buttonId = null
    } = options;

    if (buttonId) {
        const btn = document.getElementById(buttonId);
        if (btn) btn.disabled = true;
    }

    try {
        if (!window.jspdf) {
            throw new Error('jsPDF not loaded');
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');

        doc.setFontSize(18);
        doc.setFont('helvetica', 'bold');
        doc.text(title, doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });

        if (subtitle) {
            doc.setFontSize(10);
            doc.setFont('helvetica', 'italic');
            doc.text(subtitle, doc.internal.pageSize.getWidth() / 2, 22, { align: 'center' });
        }

        doc.autoTable({
            startY: subtitle ? 28 : 25,
            head: [columns.map(col => col.label)],
            body: data.map((row, index) => {
                const rowData = [];
                columns.forEach(col => {
                    rowData.push(row[col.key] || '-');
                });
                return rowData;
            }),
            theme: 'grid',
            headStyles: {
                fillColor: [68, 114, 196],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'center',
                fontSize: 9,
                cellPadding: 3
            },
            bodyStyles: {
                fontSize: 8,
                cellPadding: 3
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            margin: { top: 28, left: 10, right: 10, bottom: 15 }
        });

        const finalY = doc.lastAutoTable.finalY || 28;
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.text(`TOTAL: ${data.length} Items`, 15, finalY + 8);

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
