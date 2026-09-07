<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #222; }
        h2 { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { border: 1px solid #ddd; padding: 6px 8px; text-align: center; }
        td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
        .text-center { text-align: center; }
        .text-right { text-align: left; }
        .keluar { color: #dc2626; }
        .summary-table { width: auto; border: none; margin-bottom: 14px; }
        .summary-table td { border: none; padding: 0; }

        .ttd-table { width: 100%; margin-top: 40px; border-collapse: collapse; page-break-inside: avoid; }
        .ttd-table td { vertical-align: top; border: none; }
        .ttd-content { width: 45%; text-align: center; }
        .ttd-space { height: 35px; }
        .ttd-anchor { margin: 4px 0; }
        .ttd-nama { font-weight: bold; text-decoration: underline; margin: 0; }
        .ttd-nip { margin: 2px 0 0; }

        body { margin-bottom: 26mm; }
        .disclaimer-wrapper {
            position: fixed;
            left: 0; right: 0; bottom: 0;
            border-top: 1px solid #333;
            padding-top: 8px;
            background: #fff;
        }
        .disclaimer-table { width: 100%; border-collapse: collapse; }
        .disclaimer-table td { border: none; vertical-align: middle; padding: 0; font-size: 9px; }
        .disclaimer-logo { width: 110px; text-align: right; }
        .disclaimer-logo img { width: 100px; }
    </style>
</head>
<body>
    <h2>Laporan Keuangan</h2>

    <table class="summary-table">
        <tr>
            <td><strong>Total Pengeluaran:</strong></td>
            <td class="keluar" style="padding-left:8px;">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:10%;">Tanggal</th>
                <th style="width:28%;">Uraian Pagu</th>
                <th style="width:14%;">Vendor</th>
                <th style="width:28%;">Uraian Transaksi</th>
                <th style="width:15%;">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $i => $trx)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $trx->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $trx->budgetCategory->uraian ?? '-' }}</td>
                    <td>{{ $trx->vendor->nama_vendor ?? '-' }}</td>
                    <td>{{ $trx->uraian }}</td>
                    <td class="text-right keluar">Rp {{ number_format($trx->nominal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            @if ($transactions->isEmpty())
                <tr><td colspan="6" style="text-align:center;">Tidak ada data untuk filter ini.</td></tr>
            @endif
        </tbody>
    </table>

    <p class="narasi" style="margin-top:16px;">
        Demikian laporan realisasi anggaran ini disampaikan untuk dipergunakan sebagaimana mestinya.
    </p>

    {{-- ===== BLOK TANDA TANGAN ===== --}}
    <table class="ttd-table">
        <tr>
            <td></td>
            <td class="ttd-content">
                <p>Pangkalpinang, {{ \Carbon\Carbon::parse($tanggalCetak)->translatedFormat('d F Y') }}</p>
                <p>{{ $pejabat->jabatan ?? 'Kepala Kantor UPT BKN Pangkalpinang' }}</p>
                <div class="ttd-space"></div>
                <p class="ttd-anchor">#</p>
                <p class="ttd-nama">{{ $pejabat->nama ?? '(Nama Pejabat)' }}</p>
                <p class="ttd-nip">NIP. {{ $pejabat->nip ?? '..........................' }}</p>
            </td>
        </tr>
    </table>

    {{-- ===== DISCLAIMER TTE / BSrE ===== --}}
    <div class="disclaimer-wrapper">
        <table class="disclaimer-table">
            <tr>
                <td>
                    - UU ITE No 11 Tahun 2008 Pasal 5 Ayat 1<br>
                    "Informasi Elektronik dan/atau Dokumen Elektronik dan/atau hasil cetaknya merupakan alat bukti hukum yang sah."<br>
                    - Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan BSrE
                </td>
                <td class="disclaimer-logo">
                    <img src="{{ public_path('images/logo-bsre.png') }}" alt="Logo BSrE">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>