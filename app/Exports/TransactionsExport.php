<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
{
    protected int $nomor = 0;

    public function __construct(protected Collection $transactions) {}

    public function collection(): Collection
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Uraian Pagu', 'Vendor', 'Uraian Transaksi', 'Nominal (Rp)'];
    }

    public function map($transaction): array
    {
        $this->nomor++;

        return [
            $this->nomor,
            $transaction->tanggal->format('d-m-Y'),
            $transaction->budgetCategory->uraian ?? '-',
            $transaction->vendor->nama_vendor ?? '-',
            $transaction->uraian,
            (float) $transaction->nominal, // angka murni, biar Excel yang format tampilannya
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '#,##0', // format ribuan otomatis sesuai regional Excel, tidak akan salah baca lagi
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}