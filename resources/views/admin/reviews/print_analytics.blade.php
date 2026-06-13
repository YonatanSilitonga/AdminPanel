<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analitik Ulasan Pengguna - Dinas Kebudayaan dan Pariwisata</title>
    <style>
        /* Base page layout for print/screen */
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .print-container {
            width: 21cm; /* A4 width */
            min-height: 29.7cm; /* A4 height */
            margin: 0 auto;
            padding: 2cm;
            box-sizing: border-box;
        }

        /* Kop Surat Dinas Styles */
        .kop-header {
            display: flex;
            align-items: center;
            border-bottom: 4px double #000;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .kop-logo {
            width: 80px;
            height: auto;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .kop-text {
            flex-grow: 1;
            text-align: center;
        }

        .kop-text h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text h2 {
            font-size: 16pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text p {
            font-size: 9.5pt;
            margin: 2px 0;
            font-style: italic;
        }

        /* Document Title Block */
        .doc-title-block {
            text-align: center;
            margin-bottom: 30px;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }

        .doc-number {
            font-size: 11pt;
            margin: 5px 0 0 0;
        }

        /* Content Styles */
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 15px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 10pt;
            vertical-align: middle;
        }

        .data-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f2f2f2;
            text-transform: uppercase;
        }

        .data-table td.center {
            text-align: center;
        }
        
        .progress-bar-container {
            width: 100%;
            background-color: #e9ecef;
            border-radius: 4px;
            height: 16px;
            position: relative;
            border: 1px solid #ccc;
        }
        
        .progress-bar-fill {
            height: 100%;
            background-color: #066466;
            border-radius: 4px 0 0 4px;
        }
        
        .progress-text {
            position: absolute;
            right: 5px;
            top: 0;
            font-size: 8pt;
            line-height: 16px;
            color: #000;
            font-weight: bold;
        }

        .summary-boxes {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .summary-box {
            flex: 1;
            border: 1px solid #000;
            text-align: center;
            padding: 15px;
            margin: 0 5px;
        }

        .summary-box.first { margin-left: 0; }
        .summary-box.last { margin-right: 0; }

        .summary-box h4 {
            margin: 0 0 10px 0;
            font-size: 10pt;
            text-transform: uppercase;
            color: #555;
        }

        .summary-box p {
            margin: 0;
            font-size: 18pt;
            font-weight: bold;
        }

        /* Signature block */
        .signature-block {
            float: right;
            width: 250px;
            text-align: left;
            margin-top: 40px;
            font-size: 11pt;
            page-break-inside: avoid;
        }

        .signature-space {
            height: 70px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Clearfix for floats */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* Screen only helper */
        .screen-actions {
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 10px 20px;
            text-align: right;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .btn {
            background-color: #066466;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity 0.15s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-secondary {
            background-color: #6c757d;
            margin-right: 10px;
        }

        /* Printing Specific Styles */
        @media print {
            body {
                background-color: #fff;
                color: #000;
            }

            .print-container {
                width: auto;
                padding: 0;
                margin: 0;
            }

            .screen-actions {
                display: none;
            }

            @page {
                size: A4;
                margin: 2cm 1.5cm 2cm 2cm; /* Standard government margins */
            }

            .data-table th {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .progress-bar-container {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .progress-bar-fill {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (Visible only on screen, hidden on print) -->
    <div class="screen-actions">
        <button onclick="window.close()" class="btn btn-secondary">Tutup Halaman</button>
        <button onclick="window.print()" class="btn">
            <svg style="width: 16px; height: 16px; fill: currentColor;" viewBox="0 0 24 24">
                <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
            </svg>
            Cetak Analitik / Simpan PDF
        </button>
    </div>

    <!-- Print Document Container -->
    <div class="print-container">
        <!-- Kop Surat Dinas -->
        <header class="kop-header">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" class="kop-logo" alt="Logo Instansi">
            @else
                <img src="{{ asset('images/logo.jpeg') }}" class="kop-logo" alt="Logo Instansi" onerror="this.style.display='none'">
            @endif
            <div class="kop-text">
                @php
                    $instansiLines = explode('/', $instansi);
                @endphp
                @foreach($instansiLines as $line)
                    @if($loop->first)
                        <h1>{{ trim($line) }}</h1>
                    @else
                        <h2>{{ trim($line) }}</h2>
                    @endif
                @endforeach
                <p>Alamat: {{ $alamat }}</p>
                <p>Telp: {{ $telp }} | Email: {{ $email }} | Website: {{ $website }}</p>
            </div>
        </header>

        <!-- Judul Laporan -->
        <div class="doc-title-block">
            <h3 class="doc-title">{{ $hal }}</h3>
            <p class="doc-number">Nomor: {{ $nomorSurat }}</p>
        </div>

        <!-- Ikhtisar Data -->
        <div class="section-title">A. Ikhtisar Data Ulasan</div>
        <div class="summary-boxes">
            <div class="summary-box first">
                <h4>Total Ulasan</h4>
                <p>{{ number_format($sentimentSummary['total'] ?? 0) }}</p>
            </div>
            <div class="summary-box">
                <h4>Ulasan Positif</h4>
                <p style="color: #2e7d32;">{{ number_format($sentimentSummary['positive'] ?? 0) }}</p>
            </div>
            <div class="summary-box">
                <h4>Ulasan Netral</h4>
                <p style="color: #ed6c02;">{{ number_format($sentimentSummary['neutral'] ?? 0) }}</p>
            </div>
            <div class="summary-box last">
                <h4>Ulasan Negatif</h4>
                <p style="color: #d32f2f;">{{ number_format($sentimentSummary['negative'] ?? 0) }}</p>
            </div>
        </div>

        <!-- Distribusi Rating -->
        <div class="section-title">B. Distribusi Rating Bintang</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="15%">Rating Bintang</th>
                    <th width="15%">Jumlah Ulasan</th>
                    <th width="70%">Persentase & Visualisasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach([5, 4, 3, 2, 1] as $rating)
                    @php 
                        $data = $ratingDistribution[$rating] ?? ['count' => 0, 'percentage' => 0];
                    @endphp
                    <tr>
                        <td class="center">
                            @for($i = 0; $i < $rating; $i++)
                                ★
                            @endfor
                            @for($i = $rating; $i < 5; $i++)
                                ☆
                            @endfor
                            <br>({{ $rating }} Bintang)
                        </td>
                        <td class="center">{{ number_format($data['count']) }}</td>
                        <td>
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="width: {{ $data['percentage'] }}%;"></div>
                                <span class="progress-text">{{ $data['percentage'] }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Analisis Sentimen Lanjutan -->
        @if(isset($keywordSummary['overall']['top_keywords']) && count($keywordSummary['overall']['top_keywords']) > 0)
        <div class="section-title">C. Analisis Kata Kunci (AI)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kata Kunci yang Sering Muncul</th>
                    <th>Kemunculan</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($keywordSummary['overall']['top_keywords'], 0, 10) as $item)
                    @if(is_array($item) && !empty($item['keyword']))
                        <tr>
                            <td>{{ ucwords($item['keyword']) }}</td>
                            <td class="center">{{ $item['count'] ?? 0 }} kali</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Signature Block -->
        <div class="clearfix">
            <div class="signature-block">
                <p>Balige, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p>{{ $jabatan }}</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $namaPenandatangan }}</p>
                <p>NIP. {{ $nipPenandatangan }}</p>
            </div>
        </div>
    </div>

    <!-- Script to auto-trigger print dialog (optional) -->
    <!-- <script>window.onload = function() { window.print(); }</script> -->
</body>
</html>
