<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mode Pemeliharaan Aktif</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9fafb; margin: 0; padding: 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; margin-top: 40px; margin-bottom: 40px; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #f3f4f6;">
        <!-- Header -->
        <tr>
            <td align="center" style="background-color: #066466; padding: 40px 20px;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px;">WISATA TOBA</h1>
                <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 14px;">Mode Pemeliharaan Diaktifkan</p>
            </td>
        </tr>
        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="font-size: 16px; color: #1f2937; line-height: 1.6; margin-top: 0;">Halo Admin,</p>
                <p style="font-size: 15px; color: #4b5563; line-height: 1.6;">Mode pemeliharaan (maintenance mode) telah diaktifkan untuk platform.</p>
                
                <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 8px; margin: 25px 0;">
                    <strong style="color: #92400e; font-size: 14px; display: block; margin-bottom: 5px;">Pesan Pemeliharaan:</strong>
                    <span style="color: #b45309; font-size: 14px; line-height: 1.5;">{{ $message }}</span>
                </div>

                <p style="font-size: 14px; color: #6b7280; line-height: 1.6; margin-bottom: 0;">Pengunjung yang mengakses website saat ini akan diarahkan ke halaman pemeliharaan.</p>
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
