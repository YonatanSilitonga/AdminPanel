<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ulasan Baru Masuk</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9fafb; margin: 0; padding: 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; margin-top: 40px; margin-bottom: 40px; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #f3f4f6;">
        <!-- Header -->
        <tr>
            <td align="center" style="background-color: #066466; padding: 40px 20px;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px;">WISATA TOBA</h1>
                <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 14px;">Notifikasi Ulasan Baru</p>
            </td>
        </tr>
        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="font-size: 16px; color: #1f2937; line-height: 1.6; margin-top: 0;">Halo Admin,</p>
                <p style="font-size: 15px; color: #4b5563; line-height: 1.6;">Seorang wisatawan baru saja mengirimkan ulasan baru untuk destinasi wisata:</p>
                
                <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin: 25px 0;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td style="padding-bottom: 10px; font-size: 14px; color: #4b5563;">
                                <strong>Destinasi:</strong> {{ $review->destination->name ?? 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px; font-size: 14px; color: #4b5563;">
                                <strong>Pengulas:</strong> {{ $review->reviewer_name }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px; font-size: 14px; color: #4b5563;">
                                <strong>Rating:</strong> 
                                <span style="color: #fbbf24; font-size: 18px; font-weight: bold;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </span>
                                ({{ $review->rating }}/5)
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; font-size: 14px; color: #1f2937; font-style: italic; line-height: 1.6;">
                                "{{ $review->review }}"
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="{{ route('admin.reviews.index') }}" style="background-color: #066466; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">Lihat & Moderasi Ulasan</a>
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
