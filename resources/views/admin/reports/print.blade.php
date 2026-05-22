<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penanganan Keluhan Wisatawan - Dinas Kebudayaan dan Pariwisata</title>
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

        /* Metadata info */
        .report-meta {
            margin-bottom: 20px;
            font-size: 11pt;
        }
        
        .report-meta table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .report-meta td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .report-meta td.label {
            width: 120px;
        }
        
        .report-meta td.separator {
            width: 15px;
            text-align: center;
        }

        /* Report Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 10pt;
            vertical-align: top;
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

        .status-badge {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
        }

        /* Signature block */
        .signature-block {
            float: right;
            width: 250px;
            text-align: left;
            margin-top: 20px;
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
                margin: 2cm 1.5cm 2cm 2cm; /* Standard government margins: top, right, bottom, left */
            }

            .data-table th {
                background-color: #f2f2f2 !important;
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
            Cetak Laporan / Simpan PDF
        </button>
    </div>

    <div class="print-container">
        <!-- Kop Surat Dinas -->
        <header class="kop-header">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" class="kop-logo" alt="Logo Instansi">
            @else
                <img src="{{ asset('images/logo.jpeg') }}" class="kop-logo" alt="Logo Pemkab Toba" onerror="this.style.display='none'">
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

        <!-- Metadata / Keterangan Penapisan Laporan -->
        <div class="report-meta">
            <table>
                <tr>
                    <td class="label">Perihal</td>
                    <td class="separator">:</td>
                    <td>Rekapitulasi Laporan Pengaduan dan Penanganan Keluhan Wisatawan</td>
                </tr>
                <tr>
                    <td class="label">Periode Laporan</td>
                    <td class="separator">:</td>
                    <td>
                        @if(request('start_date') && request('end_date'))
                            {{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d F Y') }}
                        @elseif(request('start_date'))
                            Sejak {{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y') }}
                        @elseif(request('end_date'))
                            Sampai {{ \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d F Y') }}
                        @else
                            Semua Periode (Keseluruhan)
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Kriteria Saringan</td>
                    <td class="separator">:</td>
                    <td>
                        Status: <strong>{{ request('status') ? ucfirst(request('status')) : 'Semua' }}</strong> | 
                        Kategori: <strong>{{ request('reason') ? ucfirst(str_replace('_', ' ', request('reason'))) : 'Semua' }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabel Data Laporan -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 15%;">Pelapor</th>
                    <th style="width: 20%;">Destinasi / Objek</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 20%;">Deskripsi Keluhan</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $index => $report)
                    @php
                        $rawTs = $report->created_at;
                        if ($rawTs instanceof \MongoDB\BSON\UTCDateTime) {
                            $ts = \Carbon\Carbon::createFromTimestampMs((int)$rawTs->toDateTime()->format('Uv'));
                        } elseif (is_numeric($rawTs)) {
                            $ts = \Carbon\Carbon::createFromTimestampMs((int)$rawTs);
                        } elseif ($rawTs instanceof \Carbon\Carbon) {
                            $ts = $rawTs;
                        } else {
                            $ts = null;
                        }

                        $statusLabel = [
                            'pending' => 'Menunggu',
                            'reviewed' => 'Ditinjau',
                            'resolved' => 'Selesai'
                        ][$report->status] ?? ucfirst($report->status);
                    @endphp
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ $ts ? $ts->translatedFormat('d-m-Y H:i') : '-' }}</td>
                        <td>{{ $report->user_id ?? 'Anonim' }}</td>
                        <td>{{ $report->destination?->name ?? 'Umum / Lainnya' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $report->reason ?? '-')) }}</td>
                        <td>{{ $report->description ?? '-' }}</td>
                        <td class="center">
                            <span class="status-badge">{{ $statusLabel }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; font-style: italic;">
                            Tidak ada data laporan pengaduan yang sesuai dengan penyaringan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signature Section -->
        <div class="clearfix">
            <div class="signature-block">
                <p>Balige, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p>{{ $jabatan }},</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $namaPenandatangan }}</p>
                <p>NIP. {{ $nipPenandatangan }}</p>
            </div>
        </div>
    </div>

    <!-- Automatic print trigger on load -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Give image and fonts some time to load, then trigger print
            setTimeout(() => {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>
