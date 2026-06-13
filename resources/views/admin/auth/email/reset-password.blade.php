<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Admin Toba Tourism</title>
</head>
<body style="margin:0; padding:0; font-family:'Helvetica Neue', Helvetica, Arial, sans-serif; background:#f4f6f9; color:#111827;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

                    {{-- Header --}}
                    <tr>
                        <td align="center" style="padding-bottom: 24px;">
                            <div style="background:#0a3d2e; border-radius:16px; padding: 20px 32px; display:inline-block;">
                                <span style="font-size:1.3rem; font-weight:700; color:#ffffff; letter-spacing:0.5px;">
                                    🏔 Toba Tourism Admin
                                </span>
                            </div>
                        </td>
                    </tr>

                    {{-- Card --}}
                    <tr>
                        <td style="background:#ffffff; border-radius:16px; padding: 40px 40px 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">

                            {{-- Icon --}}
                            <div style="text-align:center; margin-bottom:24px;">
                                <div style="display:inline-block; background:#f0fdf4; border-radius:50%; width:64px; height:64px; line-height:64px; font-size:28px;">
                                    🔐
                                </div>
                            </div>

                            <h1 style="font-size:1.4rem; font-weight:700; color:#111827; text-align:center; margin:0 0 8px;">
                                Reset Password Admin
                            </h1>
                            <p style="font-size:0.9rem; color:#6b7280; text-align:center; margin:0 0 28px; line-height:1.6;">
                                Anda menerima email ini karena ada permintaan reset password untuk akun admin Anda.
                            </p>

                            {{-- Divider --}}
                            <div style="height:1px; background:#f3f4f6; margin-bottom:28px;"></div>

                            <p style="font-size:0.9rem; color:#374151; margin:0 0 20px; line-height:1.6;">
                                Klik tombol di bawah untuk membuat password baru. Link ini akan <strong>kedaluwarsa dalam 60 menit</strong>.
                            </p>

                            {{-- CTA Button --}}
                            <div style="text-align:center; margin: 28px 0;">
                                <a href="{{ url('/admin/reset-password/' . ($token ?? '')) }}"
                                   style="display:inline-block; background:linear-gradient(135deg,#065f46,#059669); color:#ffffff; font-size:0.95rem; font-weight:600; padding:14px 36px; border-radius:12px; text-decoration:none; letter-spacing:0.3px;">
                                    Reset Password Sekarang
                                </a>
                            </div>

                            {{-- Fallback link --}}
                            <p style="font-size:0.8rem; color:#9ca3af; margin:20px 0 0; line-height:1.6;">
                                Jika tombol tidak berfungsi, salin dan tempel URL berikut ke browser Anda:
                            </p>
                            <p style="font-size:0.78rem; color:#059669; word-break:break-all; margin: 6px 0 0;">
                                {{ url('/admin/reset-password/' . ($token ?? '')) }}
                            </p>

                            {{-- Divider --}}
                            <div style="height:1px; background:#f3f4f6; margin: 28px 0;"></div>

                            {{-- Security notice --}}
                            <div style="background:#fffbeb; border-radius:10px; padding:14px 16px; border-left:3px solid #f59e0b;">
                                <p style="font-size:0.8rem; color:#92400e; margin:0; line-height:1.6;">
                                    ⚠️ <strong>Jika Anda tidak meminta reset password</strong>, abaikan email ini. Akun Anda tetap aman dan tidak ada perubahan yang dilakukan.
                                </p>
                            </div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="text-align:center; padding: 24px 0 0;">
                            <p style="font-size:0.78rem; color:#9ca3af; margin:0; line-height:1.6;">
                                Email ini dikirim otomatis dari sistem Admin Panel Toba Tourism.<br>
                                Mohon jangan membalas email ini.
                            </p>
                            <p style="font-size:0.75rem; color:#d1d5db; margin: 8px 0 0;">
                                &copy; {{ date('Y') }} Aplikasi Wisata Toba. Hak Cipta Dilindungi.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
