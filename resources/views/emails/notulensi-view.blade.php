<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Notulensi Rapat - Buku Tamu Digital</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa;">
    
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
                                Notulensi Rapat Tersedia
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Halo <strong style="color: #084E8F;">{{ $tamu->nama_tamu }}</strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                Terima kasih atas kunjungan Anda. Notulensi rapat yang telah dilaksanakan sudah tersedia dan dapat Anda lihat untuk dokumentasi dan tindak lanjut.
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
                                            @if($kunjungan->karyawan->count() > 0)
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Peserta dari Internal</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $kunjungan->karyawan->pluck('nama_karyawan')->join(', ') }}
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Status</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px;">
                                                    : <span style="background-color: #28a745; color: white; padding: 4px 12px; border-radius: 4px; font-weight: 600; font-size: 13px;">
                                                        TERSEDIA
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 25px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                Silakan klik tombol di bawah ini untuk melihat notulensi rapat:
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 10px 0 30px 0;">
                                        <a href="{{ route('notulensi.view', ['token' => $token]) }}" 
                                           style="background-color: #084E8F; 
                                                  color: #ffffff; 
                                                  padding: 16px 40px; 
                                                  text-decoration: none; 
                                                  border-radius: 6px; 
                                                  display: inline-block; 
                                                  font-weight: 600;
                                                  font-size: 16px;
                                                  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
                                                  transition: all 0.3s ease;">
                                            Lihat Notulensi Rapat
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
                                        <a href="{{ route('notulensi.view', ['token' => $token]) }}" 
                                           style="color: #084E8F; font-size: 13px; word-break: break-all; text-decoration: none; line-height: 1.5;">
                                            {{ route('notulensi.view', ['token' => $token]) }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 30px;">
                                <tr>
                                    <td style="background-color: #e7f3ff; border-left: 4px solid #084E8F; padding: 20px; border-radius: 6px;">
                                        <p style="margin: 0 0 12px 0; font-size: 15px; color: #084E8F; font-weight: 600;">
                                            TENTANG NOTULENSI
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #495057; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 8px;">Notulensi berisi ringkasan pembahasan, keputusan, dan tindak lanjut</li>
                                            <li style="margin-bottom: 8px;">Link ini <strong>khusus untuk Anda</strong> dan tidak dapat dibagikan</li>
                                            <li style="margin-bottom: 8px;">Anda dapat <strong>melihat dan mengunduh</strong> notulensi kapan saja</li>
                                            <li>Akses ini berlaku <strong>permanen</strong> untuk referensi Anda di masa mendatang</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- What's Inside -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 20px;">
                                <tr>
                                    <td style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 20px; border-radius: 6px;">
                                        <p style="margin: 0 0 12px 0; font-size: 15px; color: #155724; font-weight: 600;">
                                            ISI NOTULENSI
                                        </p>
                                        <p style="margin: 0; color: #155724; font-size: 14px; line-height: 1.8;">
                                            Notulensi mencakup:
                                        </p>
                                        <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #155724; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 8px;"><strong>Ringkasan Pembahasan</strong>: Topik dan poin utama yang dibahas</li>
                                            <li style="margin-bottom: 8px;"><strong>Keputusan/Kesepakatan</strong>: Hasil dan kesimpulan rapat</li>
                                            <li style="margin-bottom: 8px;"><strong>Tindak Lanjut</strong>: Action items yang perlu dilakukan</li>
                                            <li><strong>Catatan Tambahan</strong>: Informasi penting lainnya</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- Follow Up Note -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 20px;">
                                <tr>
                                    <td style="background-color: #fff8e1; border-left: 4px solid #ffb300; padding: 20px; border-radius: 6px;">
                                        <p style="margin: 0 0 10px 0; font-size: 15px; color: #f57f17; font-weight: 600;">
                                            TINDAK LANJUT
                                        </p>
                                        <p style="margin: 0; color: #f57f17; font-size: 14px; line-height: 1.8;">
                                            Jika ada tindak lanjut yang memerlukan respons atau aksi dari Anda, mohon segera ditindaklanjuti sesuai kesepakatan. 
                                            Untuk pertanyaan atau koordinasi lebih lanjut, silakan hubungi peserta rapat dari internal kami.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Signature -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e9ecef;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; font-size: 15px; color: #333333;">
                                            Terima kasih atas kunjungan dan kerja samanya,
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
                                Notulensi ini bersifat rahasia dan hanya untuk keperluan Anda.
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
