<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(protected Collection $transactions) {}

    public function collection(): Collection
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'No Referensi', 'Tanggal', 'Komponen', 'Uraian Pagu', 'Vendor',
            'Uraian Transaksi', 'Nominal (Rp)', 'Diinput Oleh',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->no_referensi,
            $transaction->tanggal->format('d-m-Y'),
            $transaction->budgetCategory->masterKomponen->kode ?? '-',
            $transaction->budgetCategory->uraian ?? '-',
            $transaction->vendor->nama_vendor ?? '-',
            $transaction->uraian,
            number_format((float) $transaction->nominal, 0, ',', '.'),
            $transaction->creator->name,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
