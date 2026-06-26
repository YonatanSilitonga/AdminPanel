<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Peringatan Gangguan Sistem</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9fafb; margin: 0; padding: 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; margin-top: 40px; margin-bottom: 40px; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #f3f4f6;">
        <!-- Header -->
        <tr>
            <td align="center" style="background-color: #7f1d1d; padding: 40px 20px;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px;">WISATA TOBA</h1>
                <p style="color: #fca5a5; margin: 5px 0 0 0; font-size: 14px;">Crash Log / Error Sistem</p>
            </td>
        </tr>
        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="font-size: 16px; color: #1f2937; line-height: 1.6; margin-top: 0;">Halo Admin,</p>
                <p style="font-size: 15px; color: #4b5563; line-height: 1.6;">Terdeteksi error atau exception tidak tertangani pada sistem Wisata Toba:</p>
                
                <div style="background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 20px; margin: 25px 0;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        @if ($requestUrl)
                        <tr>
                            <td style="padding-bottom: 10px; font-size: 14px; color: #4b5563;">
                                <strong>Request URL:</strong> <code style="background-color: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-family: monospace; color: #111827;">{{ $requestUrl }}</code>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td style="padding-bottom: 10px; font-size: 14px; color: #4b5563;">
                                <strong>Pesan Error:</strong>
                                <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; border-radius: 6px; margin: 5px 0; font-family: monospace; font-size: 13px; color: #78350f; word-break: break-all;">
                                    {{ $errorMessage }}
                                </div>
                            </td>
                        </tr>
                        @if ($errorTrace)
                        <tr>
                            <td style="font-size: 14px; color: #4b5563;">
                                <strong>Stack Trace:</strong>
                                <pre style="background-color: #1e293b; color: #f8fafc; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 11px; overflow-x: auto; max-height: 250px; white-space: pre-wrap; word-break: break-all; margin: 5px 0;">{{ $errorTrace }}</pre>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>

                <p style="font-size: 14px; color: #6b7280; line-height: 1.6; margin-bottom: 0;">Silakan periksa log sistem secara menyeluruh untuk melakukan perbaikan.</p>
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
