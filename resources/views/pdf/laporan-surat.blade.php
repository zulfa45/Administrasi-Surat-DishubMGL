<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Surat Masuk - Dinas Perhubungan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #111827;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-logo {
            width: 75px;
            text-align: center;
        }
        .header-logo img {
            max-width: 65px;
            max-height: 70px;
        }
        .header-text {
            text-align: center;
        }
        .header-text h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: normal;
            letter-spacing: 1px;
        }
        .header-text h2 {
            margin: 2px 0;
            font-size: 15pt;
            font-weight: bold;
        }
        .header-text p {
            margin: 0;
            font-size: 9pt;
            color: #4b5563;
        }
        .line-double {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        .title-doc {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .period-info {
            text-align: center;
            font-size: 9.5pt;
            color: #374151;
            margin-bottom: 15px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .report-table th, .report-table td {
            border: 1px solid #111827;
            padding: 6px 8px;
            font-size: 9pt;
            vertical-align: top;
        }
        .report-table th {
            background-color: #f3f4f6;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #9ca3af;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signature-table td {
            vertical-align: top;
            font-size: 9.5pt;
        }
    </style>
</head>
<body>
    {{-- Kop Instansi --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if(!empty($kotaLogo))
                    <img src="{{ $kotaLogo }}" alt="Logo Kota">
                @endif
            </td>
            <td class="header-text">
                <h3>PEMERINTAH KOTA MAGELANG</h3>
                <h2>DINAS PERHUBUNGAN KOTA MAGELANG</h2>
                <p>Jl. Jend. Sudirman No. 84, Kota Magelang, Jawa Tengah 56125 Telp. (0293) 362205</p>
                <p>Website: dishub.magelangkota.go.id | Email: dishubmagelangkota@gmail.com</p>
            </td>
            <td class="header-logo">
                @if(!empty($dishubLogo))
                    <img src="{{ $dishubLogo }}" alt="Logo Dishub">
                @endif
            </td>
        </tr>
    </table>

    <div class="line-double"></div>

    <div class="title-doc">LAPORAN DATA SURAT MASUK</div>
    <div class="period-info">
        <strong>Periode:</strong> {{ $periodeText }}
        @if(!empty($filterInfo))
            | <strong>Filter:</strong> {{ $filterInfo }}
        @endif
        | <strong>Total:</strong> {{ count($letters) }} Surat
    </div>

    {{-- Tabel Laporan Sesuai Spesifikasi Tahap U-14 --}}
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 20%;">Nomor Surat</th>
                <th style="width: 20%;">Asal Pengirim</th>
                <th style="width: 28%;">Perihal</th>
                <th style="width: 13%;">Tanggal Diterima</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($letters as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold;">{{ $item->nomor_surat }}</td>
                <td>{{ $item->asal_surat }}</td>
                <td>{{ $item->perihal }}</td>
                <td style="text-align: center;">
                    {{ \Carbon\Carbon::parse($item->tanggal_diterima)->translatedFormat('d M Y') }}
                </td>
                <td style="text-align: center;">
                    <span class="badge">{{ strtoupper(str_replace('_', ' ', $item->status)) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #6b7280; padding: 20px;">
                    Tidak ada data surat masuk yang sesuai dengan kriteria filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tanda Tangan Mengetahui --}}
    <table class="signature-table">
        <tr>
            <td style="width: 65%;">
                <p style="margin: 0; font-size: 8.5pt; color: #4b5563;">
                    Dicetak otomatis melalui Aplikasi SIMAS Dishub Kota Magelang pada:<br>
                    {{ now()->translatedFormat('l, d F Y - H:i') }} WIB oleh: {{ auth()->user()->name }}
                </p>
            </td>
            <td style="width: 35%; text-align: center;">
                <p style="margin: 0;">Kota Magelang, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="margin: 2px 0 0 0;">Mengetahui,<br><strong>Kepala Dinas Perhubungan</strong></p>
                <div style="height: 60px;"></div>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">( .................................................... )</p>
                <p style="margin: 2px 0 0 0; font-size: 8.5pt;">NIP. ....................................................</p>
            </td>
        </tr>
    </table>
</body>
</html>
