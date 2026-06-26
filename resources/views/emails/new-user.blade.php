<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengguna Baru Terdaftar</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9fafb; margin: 0; padding: 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; margin-top: 40px; margin-bottom: 40px; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #f3f4f6;">
        <!-- Header -->
        <tr>
            <td align="center" style="background-color: #066466; padding: 40px 20px;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px;">WISATA TOBA</h1>
                <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 14px;">Registrasi Akun Baru</p>
            </td>
        </tr>
        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="font-size: 16px; color: #1f2937; line-height: 1.6; margin-top: 0;">Halo Admin,</p>
                <p style="font-size: 15px; color: #4b5563; line-height: 1.6;">Seorang pengguna baru saja mendaftarkan akun di platform Wisata Toba:</p>
                
                <div style="background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 12px; padding: 20px; margin: 25px 0;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td style="padding-bottom: 10px; font-size: 14px; color: #4b5563;">
                                <strong>Nama Lengkap:</strong> {{ $user->name }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px; font-size: 14px; color: #4b5563;">
                                <strong>Alamat Email:</strong> {{ $user->email }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 14px; color: #4b5563;">
                                <strong>Waktu Registrasi:</strong> {{ now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="{{ route('admin.users.index') }}" style="background-color: #066466; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">Kelola Pengguna</a>
                </div>
            </td>
        </tr>
        <!-- Footer -->
        <tr>
            <td align="center" style="background-color: #f9fafb; padding: 20px; border-top: 1px solid #f3f4f6; font-size: 12px; color: #9ca3af;">
                &copy; {{ date('Y') }} Wisata Toba. Hak Cipta Dilindungi.
            </td>
        </tr>
    </table>
</body>
</html>
