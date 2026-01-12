<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reset Password - Buku Tamu Digital</title>
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
                                Reset Password Resepsionis
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Halo,
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 15px; color: #555555; line-height: 1.6;">
                                Kami menerima permintaan untuk mereset password akun resepsionis Anda. 
                                Jika ini adalah Anda, silakan klik tombol di bawah untuk melanjutkan proses reset password.
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 30px 0;">
                                        <a href="{{ route('resepsionis.password.reset', ['token' => $token, 'email' => $email]) }}" 
                                           style="background: linear-gradient(135deg, #0C4777 0%, #1a5a8f 100%); 
                                                  color: #ffffff; 
                                                  padding: 16px 40px; 
                                                  text-decoration: none; 
                                                  border-radius: 6px; 
                                                  display: inline-block; 
                                                  font-weight: 600;
                                                  font-size: 16px;
                                                  box-shadow: 0 4px 12px rgba(12, 71, 119, 0.3);
                                                  transition: all 0.3s ease;">
                                            Reset Password
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
                                        <a href="{{ route('resepsionis.password.reset', ['token' => $token, 'email' => $email]) }}" 
                                           style="color: #0C4777; font-size: 13px; word-break: break-all; text-decoration: none; line-height: 1.5;">
                                            {{ route('resepsionis.password.reset', ['token' => $token, 'email' => $email]) }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Warning Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 30px;">
                                <tr>
                                    <td style="background-color: #fff8e1; border-left: 4px solid #ffb300; padding: 20px; border-radius: 6px;">
                                        <p style="margin: 0 0 12px 0; font-size: 15px; color: #f57f17; font-weight: 600;">
                                            PENTING - HARAP DIBACA
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #f57f17; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 8px;">Link ini <strong>hanya berlaku selama 60 menit</strong></li>
                                            <li style="margin-bottom: 8px;">Jika Anda <strong>tidak meminta</strong> reset password, abaikan email ini</li>
                                            <li>Untuk keamanan, <strong>jangan bagikan</strong> link ini kepada siapa pun</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- Signature -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e9ecef;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; font-size: 15px; color: #333333;">
                                            Terima kasih,
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
