<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Undangan Pembuatan Akun Resepsionis - Buku Tamu Digital</title>
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
                                Undangan Pembuatan Akun Resepsionis
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
                                Selamat! Anda telah ditambahkan sebagai <strong>Resepsionis</strong> di sistem Buku Tamu Digital. Silakan buat password untuk akun Anda dengan mengklik tombol di bawah ini.
                            </p>

                            <!-- Account Info Card -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px;">
                                            INFORMASI AKUN
                                        </p>
                                        
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; width: 150px; vertical-align: top;">
                                                    <strong>Nama</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $karyawan->nama_karyawan }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Email</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $karyawan->email_karyawan }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px; color: #495057; vertical-align: top;">
                                                    <strong>Departemen</strong>
                                                </td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #212529;">
                                                    : {{ $karyawan->departemen }}
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
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 25px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                Silakan klik tombol di bawah ini untuk membuat password Anda:
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 10px 0 30px 0;">
                                        <a href="{{ $appUrl . route('resepsionis.account.create', ['token' => $token], false) }}" 
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
                                            Buat Password Akun
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
                                        <a href="{{ $appUrl . route('resepsionis.account.create', ['token' => $token], false) }}" 
                                           style="color: #0C4777; font-size: 13px; word-break: break-all; text-decoration: none; line-height: 1.5;">
                                            {{ $appUrl . route('resepsionis.account.create', ['token' => $token], false) }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 30px;">
                                <tr>
                                    <td style="background-color: #e7f3ff; border-left: 4px solid #084E8F; padding: 20px; border-radius: 6px;">
                                        <p style="margin: 0 0 12px 0; font-size: 15px; color: #084E8F; font-weight: 600;">
                                            INFORMASI PENTING
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #495057; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 8px;">Link ini <strong>hanya berlaku untuk Anda</strong> dan tidak dapat dibagikan</li>
                                            <li style="margin-bottom: 8px;">Buat password yang <strong>kuat dan aman</strong> (minimal 8 karakter)</li>
                                            <li style="margin-bottom: 8px;">Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                                            <li>Link ini berlaku selama <strong>48 jam</strong> sejak email ini dikirim</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- Access Info -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 20px;">
                                <tr>
                                    <td style="background-color: #fff8e1; border-left: 4px solid #ffb300; padding: 20px; border-radius: 6px;">
                                        <p style="margin: 0 0 12px 0; font-size: 15px; color: #f57f17; font-weight: 600;">
                                            CARA LOGIN SETELAH MEMBUAT PASSWORD
                                        </p>
                                        <ol style="margin: 10px 0 0 0; padding-left: 20px; color: #f57f17; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 8px;">Buka halaman login resepsionis</li>
                                            <li style="margin-bottom: 8px;">Masukkan email: <strong>{{ $karyawan->email_karyawan }}</strong></li>
                                            <li style="margin-bottom: 8px;">Masukkan password yang Anda buat</li>
                                            <li>Klik tombol "Login" untuk mengakses dashboard</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <!-- Signature -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e9ecef;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; font-size: 15px; color: #333333;">
                                            Selamat bergabung dengan tim kami!
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
                                Jika Anda tidak merasa mendaftar sebagai resepsionis, silakan abaikan email ini.
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
