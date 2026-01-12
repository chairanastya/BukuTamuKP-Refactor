<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="background: #0C4777; color: white; padding: 20px; text-align: center; margin-bottom: 30px;">
        <h1 style="margin: 0; font-size: 24px;">BUKU TAMU DIGITAL</h1>
        <p style="margin: 10px 0 0 0; font-size: 16px;">Reset Password Resepsionis</p>
    </div>

    <div style="padding: 0 20px;">
        <p style="margin-bottom: 20px;">Halo,</p>

        <p style="margin-bottom: 20px;">
            Kami menerima permintaan untuk mereset password akun resepsionis Anda.
        </p>

        <p style="margin-bottom: 20px;">
            Untuk mereset password, silakan klik tombol di bawah ini:
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('resepsionis.password.reset', ['token' => $token, 'email' => $email]) }}" 
               style="background: #0C4777; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                Reset Password
            </a>
        </div>

        <p style="margin-bottom: 10px;">
            Atau salin dan tempelkan link berikut ke browser Anda:
        </p>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; word-break: break-all; margin-bottom: 30px;">
            <a href="{{ route('resepsionis.password.reset', ['token' => $token, 'email' => $email]) }}" style="color: #0C4777;">
                {{ route('resepsionis.password.reset', ['token' => $token, 'email' => $email]) }}
            </a>
        </div>

        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-bottom: 30px;">
            <strong style="color: #856404;">PERHATIAN:</strong>
            <ul style="margin: 10px 0; padding-left: 20px; color: #856404;">
                <li>Link ini hanya berlaku selama 60 menit</li>
                <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
                <li>Untuk keamanan, jangan bagikan link ini kepada siapa pun</li>
            </ul>
        </div>

        <p style="margin-bottom: 5px;">Terima kasih,</p>
        <p style="margin-top: 0; font-weight: bold;">Tim Buku Tamu Digital</p>
    </div>

</body>
</html>
