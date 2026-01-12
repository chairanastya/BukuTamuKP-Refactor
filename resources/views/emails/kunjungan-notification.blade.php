<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Notifikasi Status Kunjungan - Buku Tamu Digital</title>
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
                                Notifikasi Status Kunjungan
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Halo <strong style="color: #0C4777;">{{ $tamu->nama }}</strong>,
                            </p>

                            @if($status === 'diterima')
                                <!-- Accepted Status -->
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 25px;">
                                    <tr>
                                        <td style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left: 5px solid #28a745; padding: 25px; border-radius: 8px; text-align: center;">
                                            <div style="font-size: 48px; margin-bottom: 10px;">✓</div>
                                            <h2 style="margin: 0 0 10px 0; font-size: 22px; color: #155724; font-weight: 600;">
                                                Kunjungan Anda DITERIMA
                                            </h2>
                                            <p style="margin: 0; font-size: 15px; color: #155724;">
                                                Permintaan kunjungan Anda telah disetujui
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin: 0 0 25px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                    <strong>{{ $karyawan->nama }}</strong> akan segera menjemput Anda. 
                                    Mohon menunggu sebentar di area resepsionis. Berikut adalah detail kunjungan Anda:
                                </p>
                            @else
                                <!-- Rejected Status -->
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 25px;">
                                    <tr>
                                        <td style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border-left: 5px solid #dc3545; padding: 25px; border-radius: 8px; text-align: center;">
                                            <div style="font-size: 48px; margin-bottom: 10px;">✗</div>
                                            <h2 style="margin: 0 0 10px 0; font-size: 22px; color: #721c24; font-weight: 600;">
                                                Kunjungan Anda DITOLAK
                                            </h2>
                                            <p style="margin: 0; font-size: 15px; color: #721c24;">
                                                Mohon maaf, permintaan kunjungan tidak dapat disetujui
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin: 0 0 25px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                    Mohon maaf, <strong>{{ $karyawan->nama }}</strong> sedang tidak bisa menerima tamu saat ini. 
                                    Silakan koordinasi dengan resepsionis untuk menjadwalkan kunjungan di waktu lain.
                                </p>
                            @endif

                            <!-- Visit Details Card -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px;">
                                            DETAIL KUNJUNGAN
                                        </p>
                                        
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; width: 160px; vertical-align: top;">
                                                    <strong>Karyawan yang Dituju</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $karyawan->nama }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Jabatan</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $karyawan->jabatan }}
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
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Status</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px;">
                                                    @if($status === 'diterima')
                                                        <span style="background-color: #28a745; color: white; padding: 4px 12px; border-radius: 4px; font-weight: 600; font-size: 13px;">
                                                            DITERIMA
                                                        </span>
                                                    @else
                                                        <span style="background-color: #dc3545; color: white; padding: 4px 12px; border-radius: 4px; font-weight: 600; font-size: 13px;">
                                                            DITOLAK
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if($status === 'diterima')
                                <!-- Instructions for Accepted Visit -->
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 30px;">
                                    <tr>
                                        <td style="background-color: #e7f3ff; border-left: 4px solid #0C4777; padding: 20px; border-radius: 6px;">
                                            <p style="margin: 0 0 12px 0; font-size: 15px; color: #0C4777; font-weight: 600;">
                                                PETUNJUK KUNJUNGAN
                                            </p>
                                            <ul style="margin: 0; padding-left: 20px; color: #495057; font-size: 14px; line-height: 1.8;">
                                                <li style="margin-bottom: 8px;">Mohon tetap menunggu di area resepsionis</li>
                                                <li style="margin-bottom: 8px;">Karyawan akan datang menjemput Anda sebentar lagi</li>
                                                <li style="margin-bottom: 8px;">Siapkan identitas diri jika diperlukan</li>
                                                <li>Patuhi protokol dan peraturan yang berlaku</li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin: 0 0 20px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                    Terima kasih atas kesabaran Anda. Mohon menunggu sebentar.
                                </p>
                            @else
                                <!-- Contact Info for Rejected Visit -->
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 30px;">
                                    <tr>
                                        <td style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; border-radius: 6px;">
                                            <p style="margin: 0 0 12px 0; font-size: 15px; color: #856404; font-weight: 600;">
                                                INFORMASI KONTAK
                                            </p>
                                            <p style="margin: 0; color: #856404; font-size: 14px; line-height: 1.8;">
                                                Silakan menemui resepsionis untuk informasi lebih lanjut atau untuk menjadwalkan kunjungan di waktu lain. 
                                                Anda juga dapat menghubungi <strong>{{ $karyawan->nama }}</strong> melalui kontak resmi.
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin: 0 0 20px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                    Terima kasih atas pengertiannya. Kami harap dapat melayani Anda di kesempatan lain.
                                </p>
                            @endif

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
                                Email ini dikirim secara otomatis oleh sistem Buku Tamu Digital.<br>
                                Untuk pertanyaan, silakan hubungi resepsionis kami.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
