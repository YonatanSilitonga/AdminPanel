<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Baru Masuk</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9fafb; margin: 0; padding: 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; margin-top: 40px; margin-bottom: 40px; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #f3f4f6;">
        <!-- Header -->
        <tr>
            <td align="center" style="background-color: #dc2626; padding: 40px 20px;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px;">WISATA TOBA</h1>
                <p style="color: #fecaca; margin: 5px 0 0 0; font-size: 14px;">Notifikasi Laporan Masalah / Isu</p>
            </td>
        </tr>
        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="font-size: 16px; color: #1f2937; line-height: 1.6; margin-top: 0;">Halo Admin,</p>
                <p style="font-size: 15px; color: #4b5563; line-height: 1.6;">Seorang pengunjung melaporkan isu atau masalah terkait destinasi/ulasan:</p>
                
                <div style="background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 20px; margin: 25px 0;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td style="padding-bottom: 10px; font-size: 14px; color: #4b5563;">
                                <strong>Destinasi:</strong> {{ $report->destination->name ?? 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px; font-size: 14px; color: #4b5563;">
                                <strong>Pelapor:</strong> {{ $report->reporter_name }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px; font-size: 14px; color: #4b5563;">
                                <strong>Alasan Laporan:</strong> <span style="color: #dc2626; font-weight: bold;">{{ $report->reason }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-top: 10px; border-top: 1px solid #fca5a5; font-size: 14px; color: #1f2937; line-height: 1.6;">
                                <strong>Deskripsi Isu:</strong><br>
                                "{{ $report->description }}"
                            </td>
                        </tr>
                        @if ($report->image_url)
                        <tr>
                            <td style="padding-top: 15px; text-align: center;">
                                <img src="{{ $report->image_url }}" alt="Bukti Laporan" style="max-width: 100%; max-height: 250px; border-radius: 8px; border: 1px solid #fca5a5;">
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="{{ route('admin.reports.index') }}" style="background-color: #dc2626; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">Proses Laporan</a>
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
