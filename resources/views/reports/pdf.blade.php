<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        h2 { margin-bottom: 2px; }
        .subtitle { color: #777; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
        .text-right { text-align: right; }
        .keluar { color: #dc2626; }
        .summary { margin-bottom: 16px; }
        .summary td { border: none; padding: 2px 8px; }
    </style>
</head>
<body>
    <h2>Laporan Keuangan</h2>
    <p class="subtitle">Dicetak pada {{ now()->format('d-m-Y H:i') }} WIB</p>

    <table class="summary">
        <tr>
            <td><strong>Total Pengeluaran:</strong></td>
            <td class="keluar">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No Referensi</th>
                <th>Tanggal</th>
                <th>Komponen</th>
                <th>Uraian Pagu</th>
                <th>Vendor</th>
                <th>Uraian Transaksi</th>
                <th class="text-right">Nominal</th>
                <th>Diinput Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $trx)
                <tr>
                    <td>{{ $trx->no_referensi }}</td>
                    <td>{{ $trx->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $trx->budgetCategory->masterKomponen->kode ?? '-' }}</td>
                    <td>{{ $trx->budgetCategory->uraian ?? '-' }}</td>
                    <td>{{ $trx->vendor->nama_vendor ?? '-' }}</td>
                    <td>{{ $trx->uraian }}</td>
                    <td class="text-right keluar">Rp {{ number_format($trx->nominal, 0, ',', '.') }}</td>
                    <td>{{ $trx->creator->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
