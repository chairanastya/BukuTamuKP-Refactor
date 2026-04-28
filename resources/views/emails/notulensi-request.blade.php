<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Permintaan Pengisian Notulensi - Buku Tamu Digital</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa;">
    @php $appUrl = rtrim(config('app.public_url'), '/'); @endphp
    
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f7fa; padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Email Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #084E8F; padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; font-size: 28px; color: #ffffff; font-weight: 600; letter-spacing: 1px;">
                                BUKU TAMU DIGITAL
                            </h1>
                            <p style="margin: 12px 0 0 0; font-size: 16px; color: #ffffff; font-weight: 400; opacity: 0.9;">
                                Permintaan Pengisian Notulensi Rapat
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Halo <strong style="color: #084E8F;">{{ $karyawan->nama_karyawan }}</strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                Anda telah menerima tamu. Mohon luangkan waktu untuk mengisi notulensi rapat sebagai dokumentasi dan arsip internal.
                            </p>

                            <!-- Meeting Info Card -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px;">
                                            INFORMASI RAPAT
                                        </p>
                                        
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; width: 150px; vertical-align: top;">
                                                    <strong>Nama Tamu</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $kunjungan->tamu->nama_tamu }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Instansi</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $kunjungan->tamu->instansi_tamu ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Keperluan</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $kunjungan->tujuan_kunjungan }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Waktu Rapat</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('d F Y') }}, {{ $kunjungan->jam_mulai }} WIB
                                                </td>
                                            </tr>
                                            @if($kunjungan->karyawan->count() > 1)
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Peserta Lainnya</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $kunjungan->karyawan->where('id_karyawan', '!=', $karyawan->id_karyawan)->pluck('nama_karyawan')->join(', ') }}
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 25px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                Silakan klik tombol di bawah ini untuk mengisi notulensi rapat:
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 10px 0 30px 0;">
                                        <a href="{{ $appUrl . route('notulensi.create', ['token' => $token], false) }}" 
                                           style="background-color: #084E8F; 
                                                  color: #ffffff; 
                                                  padding: 16px 40px; 
                                                  text-decoration: none; 
                                                  border-radius: 6px; 
                                                  display: inline-block; 
                                                  font-weight: 600;
                                                  font-size: 16px;
                                                  box-shadow: 0 4px 12px rgba(12, 71, 119, 0.3);
                                                  transition: all 0.3s ease;">
                                            Isi Notulensi Rapat
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #666666;">
                                Atau salin dan tempelkan link berikut ke browser Anda:
                            </p>
                            
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="background-color: #f8f9fa; padding: 16px; border-radius: 6px; border: 1px solid #e9ecef;">
                                        <a href="{{ $appUrl . route('notulensi.create', ['token' => $token], false) }}" 
                                           style="color: #0C4777; font-size: 13px; word-break: break-all; text-decoration: none; line-height: 1.5;">
                                            {{ $appUrl . route('notulensi.create', ['token' => $token], false) }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 30px;">
                                <tr>
                                    <td style="background-color: #e7f3ff; border-left: 4px solid #084E8F; padding: 20px; border-radius: 6px;">
                                        <p style="margin: 0 0 12px 0; font-size: 15px; color: #084E8F; font-weight: 600;">
                                            INFORMASI PENGISIAN NOTULENSI
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #495057; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 8px;">Link ini <strong>hanya berlaku untuk Anda</strong> dan tidak dapat dibagikan</li>
                                            <li style="margin-bottom: 8px;">Isi notulensi dengan <strong>lengkap dan jelas</strong> untuk dokumentasi</li>
                                            <li style="margin-bottom: 8px;">Notulensi mencakup: ringkasan pembahasan, keputusan, dan tindak lanjut</li>
                                            <li>Link ini berlaku selama <strong>7 hari</strong> sejak rapat dilaksanakan</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- What to Include -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 20px;">
                                <tr>
                                    <td style="background-color: #fff8e1; border-left: 4px solid #ffb300; padding: 20px; border-radius: 6px;">
                                        <p style="margin: 0 0 12px 0; font-size: 15px; color: #f57f17; font-weight: 600;">
                                            PANDUAN PENGISIAN
                                        </p>
                                        <p style="margin: 0; color: #f57f17; font-size: 14px; line-height: 1.8;">
                                            Notulensi yang baik mencakup:
                                        </p>
                                        <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #f57f17; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 8px;"><strong>Ringkasan Pembahasan</strong>: Topik utama yang dibahas</li>
                                            <li style="margin-bottom: 8px;"><strong>Keputusan/Kesepakatan</strong>: Hasil atau kesimpulan rapat</li>
                                            <li style="margin-bottom: 8px;"><strong>Tindak Lanjut</strong>: Action items dan PIC</li>
                                            <li><strong>Catatan Tambahan</strong>: Informasi penting lainnya</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- Signature -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e9ecef;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; font-size: 15px; color: #333333;">
                                            Terima kasih atas kerja samanya,
                                        </p>
                                        <p style="margin: 0; font-size: 15px; color: #084E8F; font-weight: 600;">
                                            Tim Buku Tamu Digital
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="margin: 0 0 10px 0; font-size: 13px; color: #6c757d; line-height: 1.6;">
                                Email ini dikirim secara otomatis oleh sistem Buku Tamu Digital.<br>
                                Pengisian notulensi penting untuk dokumentasi dan arsip internal.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #adb5bd;">
                                © {{ date('Y') }} Buku Tamu Digital. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
