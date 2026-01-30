/**
 * Notulensi PDF Export Component
 * Handles custom PDF generation for notulensi documents with complex formatting
 */

export async function exportNotulensiPDF(options = {}) {
    const {
        buttonId = 'exportBtn',
        buttonTextId = 'exportBtnText',
        notulensiData = {},
        dokumentasiList = [],
        karyawanList = []
    } = options;

    // Disable button and show loading
    const exportBtn = document.getElementById(buttonId);
    const exportBtnText = document.getElementById(buttonTextId);
    const originalText = exportBtnText?.textContent || 'Export to PDF';

    if (exportBtn) exportBtn.disabled = true;
    if (exportBtnText) exportBtnText.textContent = 'Membuat PDF...';

    try {
        if (!window.jspdf) {
            throw new Error('jsPDF not loaded');
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');

        // Extract data
        const {
            namaTamu = '',
            emailTamu = '',
            instansiTamu = '-',
            tujuanKunjungan = '',
            tanggalKunjungan = '',
            hariTanggal = '',
            jamMulai = '',
            jamSelesai = '...',
            anggotaRapat = '',
            isiNotulensi = ''
        } = notulensiData;

        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        const margin = 15;
        const contentWidth = pageWidth - (margin * 2);
        let yPos = margin;

        // ========== HEADER - FORMAL STYLE ==========
        doc.setFontSize(14);
        doc.setTextColor(0, 0, 0);
        doc.setFont(undefined, 'bold');
        doc.text('NOTULENSI', pageWidth / 2, yPos, { align: 'center' });

        // Underline
        const titleWidth = doc.getTextWidth('NOTULENSI');
        doc.setLineWidth(0.5);
        doc.line(pageWidth / 2 - titleWidth / 2, yPos + 2, pageWidth / 2 + titleWidth / 2, yPos + 2);

        yPos += 15;

        // ========== INFO DASAR ==========
        doc.setFontSize(10);
        doc.setFont(undefined, 'normal');

        const labelWidth = 30;

        // Hari/Tanggal
        doc.setFont(undefined, 'bold');
        doc.text('Hari/Tanggal', margin, yPos);
        doc.setFont(undefined, 'normal');
        doc.text(': ' + hariTanggal, margin + labelWidth, yPos);
        yPos += 7;

        // Waktu
        doc.setFont(undefined, 'bold');
        doc.text('Waktu', margin, yPos);
        doc.setFont(undefined, 'normal');
        doc.text(`: Pukul ${jamMulai} - ${jamSelesai} WIB`, margin + labelWidth, yPos);
        yPos += 7;

        // ========== PESERTA ==========
        doc.setFontSize(10);
        doc.setFont(undefined, 'bold');
        doc.text('• PESERTA', margin, yPos);
        yPos += 6;

        doc.setFont(undefined, 'normal');
        doc.setFontSize(9);

        // Tamu
        doc.text(`Tamu:`, margin + 3, yPos);
        yPos += 6;
        doc.text(`- ${namaTamu}`, margin + 8, yPos);
        if (instansiTamu !== '-') {
            yPos += 5;
            doc.text(`  (${instansiTamu})`, margin + 10, yPos);
        }
        yPos += 7;

        // Karyawan Tertuju
        doc.text(`Karyawan Tertuju:`, margin + 3, yPos);
        yPos += 6;

        karyawanList.forEach((karyawan) => {
            if (yPos > pageHeight - 30) {
                doc.addPage();
                yPos = margin;
            }
            doc.text(`- ${karyawan.nama} (${karyawan.jabatan})`, margin + 8, yPos);
            yPos += 5;
        });

        // Anggota Rapat
        if (anggotaRapat) {
            yPos += 2;
            doc.text(`Anggota Lain yang Hadir:`, margin + 3, yPos);
            yPos += 6;

            const anggotaItems = anggotaRapat.split(/\r?\n/).map(s => s.trim()).filter(s => s !== '');
            anggotaItems.forEach((item, index) => {
                if (yPos > pageHeight - 30) {
                    doc.addPage();
                    yPos = margin;
                }

                const prefix = `${index + 1}. `;
                const numberWidth = doc.getTextWidth(prefix);
                const wrapped = doc.splitTextToSize(item, contentWidth - 15 - numberWidth);

                wrapped.forEach((line, lineIndex) => {
                    if (yPos > pageHeight - 30) {
                        doc.addPage();
                        yPos = margin;
                    }

                    if (lineIndex === 0) {
                        doc.text(prefix + line, margin + 8, yPos);
                    } else {
                        doc.text(line, margin + 8 + numberWidth, yPos);
                    }

                    yPos += 5;
                });
            });
        }

        yPos += 5;

        // ========== TOPIK ==========
        if (yPos > pageHeight - 40) {
            doc.addPage();
            yPos = margin;
        }

        doc.setFontSize(10);
        doc.setFont(undefined, 'bold');
        doc.text('• TOPIK', margin, yPos);
        yPos += 6;

        doc.setFontSize(9);
        doc.setFont(undefined, 'normal');
        const topikLines = doc.splitTextToSize(tujuanKunjungan, contentWidth - 5);
        topikLines.forEach(line => {
            if (yPos > pageHeight - 30) {
                doc.addPage();
                yPos = margin;
            }
            doc.text(line, margin + 3, yPos);
            yPos += 5;
        });
        yPos += 5;

        // ========== AGENDA / PEMBAHASAN ==========
        if (yPos > pageHeight - 40) {
            doc.addPage();
            yPos = margin;
        }

        doc.setFontSize(10);
        doc.setFont(undefined, 'bold');
        doc.text('• AGENDA / PEMBAHASAN', margin, yPos);
        yPos += 6;

        doc.setFontSize(9);
        doc.setFont(undefined, 'normal');

        // Render notulensi HTML content
        try {
            const notulensiEl = document.querySelector('.notulensi-content');
            if (notulensiEl) {
                yPos = renderElement(notulensiEl, yPos, 0, null, doc, pageHeight, margin, contentWidth);
            }
        } catch (err) {
            console.error('Error rendering notulensi HTML:', err);
            // Fallback: render plain text
            const notulensiParagraphs = isiNotulensi.split('\n').filter(p => p.trim() !== '');

            notulensiParagraphs.forEach((paragraph, index) => {
                if (yPos > pageHeight - 30) {
                    doc.addPage();
                    yPos = margin;
                }

                const paragraphLines = doc.splitTextToSize(paragraph.trim(), contentWidth - 5);
                paragraphLines.forEach(line => {
                    if (yPos > pageHeight - 25) {
                        doc.addPage();
                        yPos = margin;
                    }
                    doc.text(line, margin + 3, yPos);
                    yPos += 5;
                });

                if (index < notulensiParagraphs.length - 1) {
                    yPos += 3;
                }
            });

            yPos += 8;
        }

        // ========== DOKUMENTASI ==========
        if (dokumentasiList.length > 0) {
            if (yPos > pageHeight - 50) {
                doc.addPage();
                yPos = margin;
            }

            doc.setFontSize(10);
            doc.setFont(undefined, 'bold');
            doc.text('• DOKUMENTASI', margin, yPos);
            yPos += 8;

            if (exportBtnText) exportBtnText.textContent = `Memuat Gambar (0/${dokumentasiList.length})...`;

            const imagesPerRow = 2;
            const imageSpacing = 5;
            const availableWidth = contentWidth - (imageSpacing * (imagesPerRow - 1));
            const imageWidth = availableWidth / imagesPerRow;
            const imageHeight = 60;

            let currentImageIndex = 0;

            for (let row = 0; currentImageIndex < dokumentasiList.length; row++) {
                if (yPos + imageHeight + 15 > pageHeight - 20) {
                    doc.addPage();
                    yPos = margin;
                }

                for (let col = 0; col < imagesPerRow && currentImageIndex < dokumentasiList.length; col++) {
                    if (exportBtnText) exportBtnText.textContent = `Memuat Gambar (${currentImageIndex + 1}/${dokumentasiList.length})...`;

                    try {
                        const imgUrl = dokumentasiList[currentImageIndex];

                        const img = await Promise.race([
                            new Promise((resolve, reject) => {
                                const image = new Image();
                                image.crossOrigin = 'Anonymous';
                                image.onload = () => resolve(image);
                                image.onerror = reject;
                                image.src = imgUrl;
                            }),
                            new Promise((_, reject) =>
                                setTimeout(() => reject(new Error('Timeout')), 10000)
                            )
                        ]);

                        const xPos = margin + (col * (imageWidth + imageSpacing));
                        const yPosImage = yPos;

                        let imgDisplayWidth = imageWidth;
                        let imgDisplayHeight = (img.height / img.width) * imageWidth;

                        if (imgDisplayHeight > imageHeight) {
                            const scaleRatio = imageHeight / imgDisplayHeight;
                            imgDisplayWidth *= scaleRatio;
                            imgDisplayHeight = imageHeight;
                        }

                        const centeredX = xPos + (imageWidth - imgDisplayWidth) / 2;

                        doc.addImage(img, 'JPEG', centeredX, yPosImage, imgDisplayWidth, imgDisplayHeight, undefined, 'FAST');

                        doc.setFontSize(7);
                        doc.setFont(undefined, 'italic');
                        doc.setTextColor(100, 100, 100);
                        doc.text(`Gambar ${currentImageIndex + 1}`, centeredX + imgDisplayWidth / 2, yPosImage + imgDisplayHeight + 3, { align: 'center' });
                        doc.setTextColor(0, 0, 0);

                    } catch (error) {
                        console.error('Error loading image:', error);
                        doc.setDrawColor(200, 200, 200);
                        doc.setLineWidth(0.5);
                        const xPos = margin + (col * (imageWidth + imageSpacing));
                        doc.rect(xPos, yPos, imageWidth, imageHeight);

                        doc.setFontSize(7);
                        doc.setFont(undefined, 'italic');
                        doc.setTextColor(150, 150, 150);
                        doc.text(`Gambar ${currentImageIndex + 1}: Gagal dimuat`, xPos + imageWidth / 2, yPos + imageHeight / 2, { align: 'center' });
                        doc.setTextColor(0, 0, 0);
                    }

                    currentImageIndex++;
                }

                yPos += imageHeight + 15;
            }
        }

        // ========== FOOTER ==========
        const totalPages = doc.internal.pages.length - 1;
        const today = new Date();
        const footerDate = today.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        for (let i = 1; i <= totalPages; i++) {
            doc.setPage(i);

            doc.setDrawColor(180, 180, 180);
            doc.setLineWidth(0.3);
            doc.line(margin, pageHeight - 20, pageWidth - margin, pageHeight - 20);

            doc.setFontSize(7);
            doc.setTextColor(100, 100, 100);
            doc.setFont(undefined, 'italic');

            doc.text(
                `Halaman ${i} dari ${totalPages}`,
                margin,
                pageHeight - 15
            );

            doc.text(
                `Dicetak pada: ${footerDate}`,
                pageWidth / 2,
                pageHeight - 15,
                { align: 'center' }
            );

            doc.text(
                'Buku Tamu Digital',
                pageWidth - margin,
                pageHeight - 15,
                { align: 'right' }
            );
        }

        // Save PDF
        if (exportBtnText) exportBtnText.textContent = 'Menyimpan PDF...';
        const fileName = `Notulensi_${namaTamu.replace(/\s+/g, '_')}_${tanggalKunjungan.replace(/\s+/g, '_')}.pdf`;
        doc.save(fileName);

        setTimeout(() => {
            if (exportBtn) exportBtn.disabled = false;
            if (exportBtnText) exportBtnText.textContent = originalText;
        }, 1000);

    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Terjadi kesalahan saat membuat PDF. Silakan coba lagi.');
        if (exportBtn) exportBtn.disabled = false;
        if (exportBtnText) exportBtnText.textContent = originalText;
    }
}

// Helper functions for HTML rendering
function renderElement(element, currentY, indent, linkUrl, doc, pageHeight, margin, contentWidth) {
    let yPos = currentY;
    const tagName = element.tagName ? element.tagName.toLowerCase() : 'text';
    const textContent = element.textContent || element.nodeValue || '';

    if (element.nodeType === Node.TEXT_NODE && textContent.trim()) {
        const computedStyle = window.getComputedStyle(element.parentElement || element);
        const fontWeight = computedStyle.fontWeight;
        const fontStyle = computedStyle.fontStyle;
        const textDecoration = computedStyle.textDecorationLine || '';

        let pdfFontStyle = 'normal';
        if (fontWeight >= 600 || fontWeight === 'bold') {
            pdfFontStyle = 'bold';
        }
        if (fontStyle === 'italic') {
            pdfFontStyle = pdfFontStyle === 'bold' ? 'bolditalic' : 'italic';
        }

        doc.setFont(undefined, pdfFontStyle);

        if (!linkUrl) {
            const color = computedStyle.color;
            if (color) {
                const rgb = color.match(/\d+/g);
                if (rgb && rgb.length >= 3) {
                    doc.setTextColor(parseInt(rgb[0]), parseInt(rgb[1]), parseInt(rgb[2]));
                }
            }
        }

        const textLines = doc.splitTextToSize(textContent.trim(), contentWidth - 5 - indent);
        textLines.forEach((line, lineIndex) => {
            if (yPos > pageHeight - 25) {
                doc.addPage();
                yPos = margin;
            }

            const x = margin + 3 + indent;

            if (linkUrl) {
                doc.setTextColor(26, 115, 232);
                doc.text(line, x, yPos);

                const textWidth = doc.getTextWidth(line);
                doc.setLineWidth(0.2);
                doc.setDrawColor(26, 115, 232);
                doc.line(x, yPos + 1, x + textWidth, yPos + 1);

                doc.setTextColor(0, 0, 0);
                const textHeight = 4;
                doc.link(x, yPos - textHeight, textWidth, textHeight + 2, { url: linkUrl });
            } else {
                doc.text(line, x, yPos);

                if (textDecoration.includes('underline')) {
                    const textWidth = doc.getTextWidth(line);
                    doc.setLineWidth(0.2);
                    doc.setDrawColor(0, 0, 0);
                    doc.line(x, yPos + 1, x + textWidth, yPos + 1);
                }

                if (textDecoration.includes('line-through')) {
                    const textWidth = doc.getTextWidth(line);
                    const textHeight = doc.getTextDimensions(line).h;
                    doc.setLineWidth(0.2);
                    doc.setDrawColor(0, 0, 0);
                    doc.line(x, yPos - textHeight / 4, x + textWidth, yPos - textHeight / 4);
                }
            }

            yPos += 4;
        });

        doc.setTextColor(0, 0, 0);
        return yPos;
    }

    if (element.nodeType === Node.ELEMENT_NODE) {
        switch (tagName) {
            case 'h1':
                doc.setFontSize(14);
                doc.setFont(undefined, 'bold');
                yPos = renderChildren(element, yPos, indent, linkUrl, doc, pageHeight, margin, contentWidth);
                yPos += 4;
                doc.setFontSize(9);
                return yPos;

            case 'h2':
                doc.setFontSize(12);
                doc.setFont(undefined, 'bold');
                yPos = renderChildren(element, yPos, indent, linkUrl, doc, pageHeight, margin, contentWidth);
                yPos += 3;
                doc.setFontSize(9);
                return yPos;

            case 'h3':
                doc.setFontSize(11);
                doc.setFont(undefined, 'bold');
                yPos = renderChildren(element, yPos, indent, linkUrl, doc, pageHeight, margin, contentWidth);
                yPos += 3;
                doc.setFontSize(9);
                return yPos;

            case 'h4':
            case 'h5':
            case 'h6':
                doc.setFontSize(10);
                doc.setFont(undefined, 'bold');
                yPos = renderChildren(element, yPos, indent, linkUrl, doc, pageHeight, margin, contentWidth);
                yPos += 2;
                doc.setFontSize(9);
                return yPos;

            case 'p':
                doc.setFontSize(9);
                yPos = renderChildren(element, yPos, indent, linkUrl, doc, pageHeight, margin, contentWidth);
                yPos += 2;
                return yPos;

            case 'a':
                const href = element.getAttribute('href');
                if (href) {
                    yPos = renderChildren(element, yPos, indent, href, doc, pageHeight, margin, contentWidth);
                } else {
                    yPos = renderChildren(element, yPos, indent, linkUrl, doc, pageHeight, margin, contentWidth);
                }
                return yPos;

            case 'ul':
                yPos = renderList(element, yPos, indent, 'bullet', linkUrl, doc, pageHeight, margin, contentWidth);
                return yPos;

            case 'ol':
                yPos = renderList(element, yPos, indent, 'numbered', linkUrl, doc, pageHeight, margin, contentWidth);
                return yPos;

            case 'li':
                yPos = renderChildren(element, yPos, indent, linkUrl, doc, pageHeight, margin, contentWidth);
                return yPos;

            case 'blockquote':
                doc.setFontSize(9);
                doc.setTextColor(100, 100, 100);
                yPos = renderChildren(element, yPos, indent + 5, linkUrl, doc, pageHeight, margin, contentWidth);
                doc.setTextColor(0, 0, 0);
                yPos += 2;
                return yPos;

            case 'br':
                return yPos + 4;

            default:
                yPos = renderChildren(element, yPos, indent, linkUrl, doc, pageHeight, margin, contentWidth);
                return yPos;
        }
    }

    return yPos;
}

function renderChildren(element, currentY, indent, linkUrl, doc, pageHeight, margin, contentWidth) {
    let yPos = currentY;
    const children = element.childNodes;

    for (let i = 0; i < children.length; i++) {
        yPos = renderElement(children[i], yPos, indent, linkUrl, doc, pageHeight, margin, contentWidth);
    }

    return yPos;
}

function renderList(listElement, currentY, indent, type, linkUrl, doc, pageHeight, margin, contentWidth) {
    let yPos = currentY;
    const items = listElement.children;

    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        if (item.tagName && item.tagName.toLowerCase() === 'li') {
            if (yPos > pageHeight - 25) {
                doc.addPage();
                yPos = margin;
            }

            const bullet = type === 'bullet' ? '•' : `${i + 1}.`;
            doc.setFontSize(9);
            doc.setFont(undefined, 'normal');

            doc.text(bullet, margin + 3 + indent, yPos);
            yPos = renderElement(item, yPos, indent + 7, linkUrl, doc, pageHeight, margin, contentWidth);
        }
    }

    return yPos + 2;
}

export default {
    exportNotulensiPDF
};
