# Button Component Documentation

## Overview

Komponen button yang reusable untuk aplikasi Buku Tamu Digital. Komponen ini menyediakan berbagai varian button dengan styling yang konsisten sesuai dengan design system aplikasi.

## Location

- **Component File**: `resources/views/components/button.blade.php`
- **Test File (HTML)**: `tests/Feature/ComponentButtonTest.html`
- **Test File (PHPUnit)**: `tests/Feature/ButtonComponentTest.php`

## Usage

### Basic Usage

```blade
<x-button>
    Click Me
</x-button>
```

### Button Variants

#### Primary Button
Digunakan untuk aksi utama seperti membuat kunjungan baru.

```blade
<x-button variant="primary" href="{{ route('resepsionis.kunjungan.create') }}" icon="heroicon-o-plus">
    Buat Kunjungan Baru
</x-button>
```

#### Export Button
Digunakan untuk export data ke Excel.

```blade
<x-button variant="export" onclick="exportToExcel()" icon="heroicon-o-arrow-down-tray">
    Export to Excel
</x-button>
```

#### Export PDF Button
Digunakan untuk export data ke PDF.

```blade
<x-button variant="export-pdf" onclick="exportToPDF()" icon="heroicon-o-document-text">
    Export to PDF
</x-button>
```

#### Success Button
Digunakan untuk aksi konfirmasi positif seperti menerima kunjungan.

```blade
<x-button variant="success" onclick="acceptKunjungan()">
    Terima
</x-button>
```

#### Danger Button
Digunakan untuk aksi destruktif seperti menolak atau menghapus.

```blade
<x-button variant="danger" onclick="openRejectModal()">
    Tolak
</x-button>
```

#### View Button
Digunakan untuk melihat detail atau hasil.

```blade
<x-button variant="view" onclick="viewHasil()">
    Lihat Hasil
</x-button>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | string | `'primary'` | Button style variant: `'primary'`, `'export'`, `'export-pdf'`, `'success'`, `'danger'`, `'view'` |
| `type` | string | `'button'` | Button type: `'button'`, `'submit'`, `'reset'` |
| `href` | string\|null | `null` | If provided, renders as `<a>` tag instead of `<button>` |
| `icon` | string\|null | `null` | SVG icon name (e.g., `'heroicon-o-plus'`) |
| `iconClass` | string | `'w-5 h-5'` | CSS classes for the icon |
| `loading` | boolean | `false` | Whether button is in loading state |
| `loadingId` | string\|null | `null` | ID prefix for loading spinner elements |

## Additional Attributes

Komponen mendukung semua HTML attributes standar seperti:
- `id` - ID element
- `class` - Additional CSS classes (akan di-merge dengan default classes)
- `onclick` - JavaScript onclick handler
- `disabled` - Disable button
- `data-*` - Data attributes
- Dan lainnya

## Examples

### Button dengan Icon

```blade
<x-button variant="primary" icon="heroicon-o-plus">
    Tambah Data
</x-button>
```

### Button sebagai Link

```blade
<x-button variant="primary" href="/karyawan/create" icon="heroicon-o-plus">
    Tambah Karyawan Baru
</x-button>
```

### Button dengan Custom Class

```blade
<x-button variant="success" class="flex-1">
    Terima
</x-button>
```

### Button dengan Loading State

```blade
<x-button id="acceptButton" variant="success" onclick="confirmAccept()">
    <span id="acceptButtonText">Terima</span>
    <svg id="acceptSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
</x-button>
```

### Disabled Button

```blade
<x-button variant="primary" disabled>
    Tidak Tersedia
</x-button>
```

## Color Palette

| Variant | Background | Hover | Usage |
|---------|-----------|-------|-------|
| Primary | `#0C4777` | `#F59E0B` | Main actions |
| Export | `#059669` | `#047857` | Excel export |
| Export PDF | `#DC2626` | `#B91C1C` | PDF export |
| Success | `#10B981` | `#059669` | Positive actions |
| Danger | `#EF4444` | `#DC2626` | Destructive actions |
| View | `#F59E0B` | `#D97706` | View details |

## Testing

### HTML Test
Buka file `tests/Feature/ComponentButtonTest.html` di browser untuk melihat visual testing dari semua varian button.

### PHPUnit Test
Jalankan test dengan command:

```bash
php artisan test --filter ButtonComponentTest
```

atau dengan Pest:

```bash
./vendor/bin/pest tests/Feature/ButtonComponentTest.php
```

## Files Modified

Komponen button ini telah diimplementasikan di file-file berikut:

1. **dashboard.blade.php**
   - Export to Excel button
   - Export to PDF button
   - Buat Kunjungan Baru button (link)
   - Terima button (dalam modal)
   - Tolak button (dalam modal)

2. **riwayat.blade.php**
   - Export to Excel button
   - Export to PDF button

3. **karyawan.blade.php**
   - Tambah Karyawan Baru button (link)

## Migration Guide

### Before (Old Style)

```blade
<button onclick="exportToExcel()" class="btn-export flex items-center gap-2">
    @svg('heroicon-o-arrow-down-tray', 'w-5 h-5')
    Export to Excel
</button>
```

### After (New Component)

```blade
<x-button variant="export" onclick="exportToExcel()" icon="heroicon-o-arrow-down-tray">
    Export to Excel
</x-button>
```

## Benefits

1. **Konsistensi**: Semua button menggunakan style yang sama
2. **Maintainability**: Update style di satu tempat akan mengupdate semua button
3. **Reusability**: Mudah digunakan di berbagai tempat dengan props yang fleksibel
4. **Type Safety**: Props yang jelas dan terdokumentasi
5. **Accessibility**: Built-in support untuk disabled state dan loading state

## Notes

- Button di dalam DataTables render function masih menggunakan inline HTML karena keterbatasan dynamic rendering
- Komponen mendukung SVG icons menggunakan blade-icons package
- Semua button memiliki transition effect untuk better UX
- Disabled state otomatis mengubah opacity dan cursor
