<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12px; color: #000; line-height: 1.5; }

        /* Kop surat */
        .kop-surat { width: 100%; margin-bottom: 4px; }
        .kop-surat td { vertical-align: middle; }
        .kop-logo { width: 70px; }
        .kop-logo img { width: 65px; height: 65px; }
        .kop-text { text-align: center; }
        .kop-text .instansi { font-size: 18px; font-weight: bold; letter-spacing: 1px; margin: 0; }
        .kop-text .unit { font-size: 15px; font-weight: bold; margin: 0; }
        .kop-text .alamat { font-size: 10px; margin: 2px 0 0; }
        .kop-divider-thick { border-top: 3px solid #000; margin-top: 6px; }
        .kop-divider-thin { border-top: 1px solid #000; margin-top: 2px; margin-bottom: 18px; }

        .judul-laporan { text-align: center; font-size: 15px; font-weight: bold; text-decoration: underline; margin-bottom: 2px; text-transform: uppercase; }
        .sub-judul { text-align: center; font-size: 12px; margin-bottom: 20px; }

        p.narasi { text-align: justify; margin-bottom: 12px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 5px 7px; font-size: 11px; }
        table.data-table th { background: #e5e5e5; font-weight: bold; text-align: center; }
        table.data-table td.num { text-align: right; }
        table.data-table td.center { text-align: center; }

        table.ringkasan { width: 60%; border-collapse: collapse; margin-bottom: 18px; }
        table.ringkasan td { border: 1px solid #000; padding: 6px 10px; font-size: 12px; }
        table.ringkasan td.label { font-weight: bold; width: 60%; }
        table.ringkasan td.value { text-align: right; }

        .section-title { font-weight: bold; font-size: 13px; margin: 18px 0 8px; text-transform: uppercase; }

        .ttd-table { width: 100%; margin-top: 40px; border-collapse: collapse; page-break-inside: avoid; }
        .ttd-table td { vertical-align: top; }
        .ttd-content { width: 45%; text-align: center; }
        .ttd-space { height: 60px; }
        .ttd-nama { font-weight: bold; text-decoration: underline; margin: 0; }
        .ttd-nip { margin: 2px 0 0; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    {{-- ===== KOP SURAT (tampil sekali di awal) ===== --}}
    <table class="kop-surat">
        <tr>
            <td class="kop-logo">
                <img src="{{ public_path('images/logo-bkn.png') }}" alt="Logo BKN">
            </td>
            <td class="kop-text">
                <p class="instansi">BADAN KEPEGAWAIAN NEGARA</p>
                <p class="unit">UPT BKN PANGKALPINANG</p>
                <p class="alamat">Jl. Jalan M. Saleh Zainudin, Kelurahan Air Salemba, Kecamatan Gabek, Kota Pangkalpinang, Kepulauan Bangka Belitung 33172</p>
            </td>
            <td class="kop-logo"></td>
        </tr>
    </table>
    <div class="kop-divider-thick"></div>
    <div class="kop-divider-thin"></div>

    <p class="judul-laporan">Laporan Realisasi Anggaran</p>
    <p class="sub-judul">
        @if ($laporanPerTahun->count() > 1)
            Tahun Anggaran {{ $laporanPerTahun->pluck('tahun.tahun')->implode(', ') }}
        @else
            Tahun Anggaran {{ $laporanPerTahun->first()['tahun']->tahun }}
        @endif
    </p>

    {{-- ===== LOOP PER TAHUN ANGGARAN ===== --}}
    @foreach ($laporanPerTahun as $i => $lap)
        @if ($i > 0)
            <div class="page-break"></div>
            <p class="judul-laporan" style="font-size:13px;">Tahun Anggaran {{ $lap['tahun']->tahun }}</p>
        @endif

        <p class="narasi">
            Sehubungan dengan pelaksanaan anggaran pada UPT BKN Pangkalpinang untuk Tahun Anggaran
            {{ $lap['tahun']->tahun }}, bersama ini kami sampaikan laporan realisasi anggaran dengan
            rincian sebagai berikut:
        </p>

        <table class="ringkasan">
            <tr>
                <td class="label">Total Pagu Anggaran</td>
                <td class="value">Rp {{ number_format($lap['totalPagu'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Realisasi</td>
                <td class="value">Rp {{ number_format($lap['totalRealisasi'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Sisa Anggaran</td>
                <td class="value">Rp {{ number_format($lap['sisa'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Persentase Serapan Anggaran</td>
                <td class="value">{{ $lap['persenSerapan'] }}%</td>
            </tr>
        </table>

        <p class="narasi">
            Berdasarkan ringkasan di atas, realisasi anggaran Tahun Anggaran {{ $lap['tahun']->tahun }}
            telah mencapai <strong>{{ $lap['persenSerapan'] }}%</strong> dari total pagu yang dialokasikan,
            dengan sisa anggaran sebesar <strong>Rp {{ number_format($lap['sisa'], 0, ',', '.') }}</strong>.
        </p>

        <p class="section-title">Rincian Realisasi per Komponen — {{ $lap['tahun']->tahun }}</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:8%;">Kode</th>
                    <th style="width:32%;">Uraian</th>
                    <th style="width:18%;">Pagu (Rp)</th>
                    <th style="width:18%;">Realisasi (Rp)</th>
                    <th style="width:16%;">Sisa (Rp)</th>
                    <th style="width:8%;">%</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lap['categories'] as $cat)
                    <tr>
                        <td class="center">{{ $cat->masterKomponen->kode ?? '-' }}</td>
                        <td>{{ $cat->uraian }}</td>
                        <td class="num">{{ number_format($cat->pagu_anggaran, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($cat->total_terpakai, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($cat->pagu_anggaran - $cat->total_terpakai, 0, ',', '.') }}</td>
                        <td class="center">{{ $cat->persentase_terpakai }}%</td>
                    </tr>
                @endforeach
                @if ($lap['categories']->isEmpty())
                    <tr><td colspan="6" class="center">Belum ada pagu anggaran untuk tahun ini.</td></tr>
                @endif
            </tbody>
        </table>

        <p class="section-title">Rincian Transaksi — {{ $lap['tahun']->tahun }}</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:12%;">No. Referensi</th>
                    <th style="width:9%;">Tanggal</th>
                    <th style="width:20%;">Komponen / Pagu</th>
                    <th style="width:14%;">Vendor</th>
                    <th style="width:23%;">Uraian</th>
                    <th style="width:14%;">Nominal (Rp)</th>
                    <th style="width:8%;">Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lap['transactions'] as $trx)
                    <tr>
                        <td>{{ $trx->no_referensi }}</td>
                        <td class="center">{{ $trx->tanggal->format('d-m-Y') }}</td>
                        <td>[{{ $trx->budgetCategory->masterKomponen->kode ?? '-' }}] {{ $trx->budgetCategory->uraian ?? '-' }}</td>
                        <td>{{ $trx->vendor->nama_vendor ?? '-' }}</td>
                        <td>{{ $trx->uraian }}</td>
                        <td class="num">{{ number_format($trx->nominal, 0, ',', '.') }}</td>
                        <td>{{ $trx->creator->name }}</td>
                    </tr>
                @endforeach
                @if ($lap['transactions']->isEmpty())
                    <tr><td colspan="7" class="center">Belum ada transaksi untuk tahun ini.</td></tr>
                @endif
            </tbody>
        </table>
    @endforeach

    <p class="narasi" style="margin-top:16px;">
        Demikian laporan realisasi anggaran ini disampaikan untuk dipergunakan sebagaimana mestinya.
    </p>

    {{-- ===== BLOK TANDA TANGAN (tampil sekali di akhir) ===== --}}
    <table class="ttd-table">
        <tr>
            <td></td>
            <td class="ttd-content">
                <p>Pangkalpinang, {{ now()->translatedFormat('d F Y') }}</p>
                <p>{{ $pejabat->jabatan ?? 'Kepala UPT BKN Pangkalpinang' }},</p>
                <div class="ttd-space"></div>
                <p class="ttd-nama">{{ $pejabat->nama ?? '(Nama Pejabat)' }}</p>
                <p class="ttd-nip">NIP. {{ $pejabat->nip ?? '..........................' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
