<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Konfirmasi Kunjungan - Buku Tamu Digital</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa;">
    
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f7fa; padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Email Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0C4777 0%, #1a5a8f 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; font-size: 28px; color: #ffffff; font-weight: 600; letter-spacing: 1px;">
                                BUKU TAMU DIGITAL
                            </h1>
                            <p style="margin: 12px 0 0 0; font-size: 16px; color: #e0e7ef; font-weight: 400;">
                                Tamu Sedang Menunggu
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Halo <strong style="color: #0C4777;">{{ $karyawan->nama }}</strong>,
                            </p>

                            <p style="margin: 0 0 25px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                Saat ini ada tamu yang <strong>sedang menunggu</strong> di resepsionis untuk bertemu dengan Anda. Mohon konfirmasi apakah Anda bisa menerima tamu berikut:
                            </p>

                            <!-- Visitor Info Card -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <p style="margin: 0 0 8px 0; font-size: 13px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px;">
                                            INFORMASI TAMU
                                        </p>
                                        
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; width: 140px; vertical-align: top;">
                                                    <strong>Nama Tamu</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $tamu->nama }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Instansi</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $tamu->instansi }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>No. Telepon</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $tamu->no_hp }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Keperluan</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $kunjungan->keperluan }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Waktu Kunjungan</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('d F Y, H:i') }} WIB
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 25px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                Silakan pilih salah satu tindakan di bawah ini:
                            </p>

                            <!-- Action Buttons -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 0 0 15px 0;">
                                        <a href="{{ route('kunjungan.confirm', ['token' => $token, 'action' => 'terima']) }}" 
                                           style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
                                                  color: #ffffff; 
                                                  padding: 16px 50px; 
                                                  text-decoration: none; 
                                                  border-radius: 6px; 
                                                  display: inline-block; 
                                                  font-weight: 600;
                                                  font-size: 16px;
                                                  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
                                                  min-width: 200px;
                                                  text-align: center;">
                                            ✓ Terima & Jemput Tamu
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 0;">
                                        <a href="{{ route('kunjungan.confirm', ['token' => $token, 'action' => 'tolak']) }}" 
                                           style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); 
                                                  color: #ffffff; 
                                                  padding: 16px 50px; 
                                                  text-decoration: none; 
                                                  border-radius: 6px; 
                                                  display: inline-block; 
                                                  font-weight: 600;
                                                  font-size: 16px;
                                                  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
                                                  min-width: 200px;
                                                  text-align: center;">
                                            ✗ Tidak Bisa Menerima
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 30px;">
                                <tr>
                                    <td style="background-color: #e7f3ff; border-left: 4px solid #0C4777; padding: 20px; border-radius: 6px;">
                                        <p style="margin: 0 0 10px 0; font-size: 15px; color: #0C4777; font-weight: 600;">
                                            INFORMASI
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #495057; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 8px;">Tamu <strong>sedang menunggu</strong> di area resepsionis</li>
                                            <li style="margin-bottom: 8px;">Tamu akan mendapat notifikasi setelah Anda melakukan konfirmasi</li>
                                            <li>Jika tidak bisa menerima, tamu akan diarahkan untuk menjadwalkan ulang</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- Signature -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e9ecef;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; font-size: 15px; color: #333333;">
                                            Terima kasih atas perhatiannya,
                                        </p>
                                        <p style="margin: 0; font-size: 15px; color: #0C4777; font-weight: 600;">
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
                                Email ini dikirim secara otomatis oleh sistem Buku Tamu Digital.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
