<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Disposisi - {{ $letter->nomor_surat }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 2cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #111827;
            line-height: 1.4;
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
            width: 70px;
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
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #111827;
            padding: 6px 10px;
            font-size: 10pt;
            vertical-align: top;
        }
        .data-table th {
            background-color: #f3f4f6;
            text-align: left;
            font-weight: bold;
        }
        .sub-header {
            background-color: #e5e7eb;
            font-weight: bold;
            padding: 5px 10px;
            border: 1px solid #111827;
            font-size: 10.5pt;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-table td {
            vertical-align: top;
            font-size: 10pt;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #9ca3af;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
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
                <h3>PEMERINTAH KOTA</h3>
                <h2>DINAS PERHUBUNGAN</h2>
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

    <div class="title-doc">LEMBAR DISPOSISI SURAT</div>

    {{-- Informasi Surat --}}
    <table class="data-table">
        <tr>
            <th style="width: 25%;">Nomor Surat</th>
            <td style="width: 75%; font-weight: bold;">{{ $letter->nomor_surat }}</td>
        </tr>
        <tr>
            <th>Asal Pengirim</th>
            <td>{{ $letter->asal_surat }}</td>
        </tr>
        <tr>
            <th>Tanggal Surat</th>
            <td>{{ \Carbon\Carbon::parse($letter->tanggal_surat)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <th>Tanggal Diterima</th>
            <td>{{ \Carbon\Carbon::parse($letter->tanggal_diterima)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <th>Sifat Surat</th>
            <td>
                <span class="badge">{{ strtoupper($letter->sifat) }}</span>
            </td>
        </tr>
        <tr>
            <th>Perihal</th>
            <td style="font-weight: bold; font-size: 10.5pt;">{{ $letter->perihal }}</td>
        </tr>
        <tr>
            <th>Keterangan / Ringkasan</th>
            <td>{{ $letter->keterangan ?: '-' }}</td>
        </tr>
    </table>

    {{-- Instruksi & Riwayat Disposisi --}}
    <div class="sub-header">DISTRIBUSI & INSTRUKSI DISPOSISI</div>
    <table class="data-table" style="margin-top: -1px;">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 25%;">Tujuan Disposisi</th>
                <th style="width: 20%;">Tgl Disposisi</th>
                <th style="width: 35%;">Instruksi / Catatan</th>
                <th style="width: 15%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($letter->assignments as $index => $assignment)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $assignment->user?->name ?? 'Bagian' }}</strong><br>
                    <small style="color: #4b5563;">{{ $assignment->department?->name ?? ($assignment->user?->department?->name ?? '-') }}</small>
                </td>
                <td>{{ \Carbon\Carbon::parse($assignment->tanggal_disposisi)->translatedFormat('d M Y') }}</td>
                <td>
                    {{ $assignment->catatan ?: '-' }}
                    @if($assignment->catatan_tindak_lanjut)
                        <div style="margin-top: 5px; font-style: italic; font-size: 9pt; color: #1f2937;">
                            <strong>Respon:</strong> "{{ $assignment->catatan_tindak_lanjut }}"
                        </div>
                    @endif
                </td>
                <td style="text-align: center;">
                    <span class="badge">{{ strtoupper(str_replace('_', ' ', $assignment->status)) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #6b7280; padding: 15px;">
                    Belum ada instruksi disposisi tercatat untuk surat ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tanda Tangan --}}
    <table class="signature-table">
        <tr>
            <td style="width: 60%;">
                <p style="margin: 0; font-size: 9pt; color: #4b5563;">
                    Dicetak melalui SIMAS Dishub Kota pada:<br>
                    {{ now()->translatedFormat('l, d F Y - H:i') }} WIB
                </p>
            </td>
            <td style="width: 40%; text-align: center;">
                <p style="margin: 0;">Pejabat Pemberi Disposisi,</p>
                <div style="height: 60px;"></div>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">( .................................................... )</p>
                <p style="margin: 2px 0 0 0; font-size: 9pt;">NIP. ....................................................</p>
            </td>
        </tr>
    </table>
</body>
</html>
