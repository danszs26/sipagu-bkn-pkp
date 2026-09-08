<x-app-layout>
<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transaksi</h2>
        <a href="{{ route('transactions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md">+ Tambah Transaksi</a>
    </div>
</x-slot>

<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

        @if (session('success'))
            <div class="bg-green-50 text-green-700 text-sm px-4 py-2.5 rounded-md border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" class="bg-white rounded-lg shadow p-3 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Anggaran</label>
                <select name="tahun_anggaran_id" class="rounded-md border-gray-300 text-sm">
                    <option value="">Tahun Berjalan (default)</option>
                    @foreach ($tahunList as $t)
                        <option value="{{ $t->id }}" {{ request('tahun_anggaran_id') == $t->id ? 'selected' : '' }}>{{ $t->tahun }} {{ $t->is_active ? '(Berjalan)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                <select name="bulan" class="rounded-md border-gray-300 text-sm">
                    <option value="">Semua Bulan</option>
                    @foreach (['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $val => $label)
                        <option value="{{ $val }}" {{ request('bulan') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="rounded-md border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="rounded-md border-gray-300 text-sm">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Terapkan Filter</button>
        </form>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-gray-50 text-gray-600 text-left">
                    <tr>
                        <th class="px-3 py-2">No Referensi</th>
                        <th class="px-3 py-2">Tanggal</th>
                        <th class="px-3 py-2">Komponen</th>
                        <th class="px-3 py-2">Pagu / Uraian</th>
                        <th class="px-3 py-2">Vendor</th>
                        <th class="px-3 py-2">Uraian Transaksi</th>
                        <th class="px-3 py-2 text-right">Nominal</th>
                        <th class="px-3 py-2">Diinput oleh</th>
                        <th class="px-3 py-2 text-center">Bukti</th>
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($transactions as $trx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-1.5 font-mono">{{ $trx->no_referensi }}</td>
                            <td class="px-3 py-1.5">{{ $trx->tanggal->format('d-m-Y') }}</td>
                            <td class="px-3 py-1.5">
                                <span class="bg-gray-100 text-gray-600 font-mono px-1.5 py-0.5 rounded">{{ $trx->budgetCategory->masterKomponen->kode ?? '-' }}</span>
                            </td>
                            <td class="px-3 py-1.5 max-w-[220px] whitespace-normal break-words">{{ $trx->budgetCategory->uraian ?? '-' }}</td>
                            <td class="px-3 py-1.5 max-w-[120px] whitespace-normal break-words">{{ $trx->vendor->nama_vendor ?? '—' }}</td>
                            <td class="px-3 py-1.5 max-w-[200px] whitespace-normal break-words">{{ $trx->uraian }}</td>
                            <td class="px-3 py-1.5 text-right font-medium text-red-600 whitespace-nowrap">Rp {{ number_format($trx->nominal, 0, ',', '.') }}</td>
                            <td class="px-3 py-1.5 text-gray-500 whitespace-nowrap">{{ $trx->creator->name }}</td>
                            <td class="px-3 py-1.5 text-center">
                                <a href="{{ route('transactions.bukti', $trx) }}" target="_blank" title="Lihat bukti" class="inline-flex text-blue-600 hover:text-blue-600">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </a>
                            </td>
                            <td class="px-3 py-1.5">
                                <div class="flex items-center justify-center gap-2">
                                    @can('update', $trx)
                                        <a href="{{ route('transactions.edit', $trx) }}" title="Edit" class="inline-flex text-blue-600 hover:text-blue-600">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5V18a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18V7.5A2.25 2.25 0 015.25 5.25h4.5"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('delete', $trx)
                                        <form action="{{ route('transactions.destroy', $trx) }}" method="POST" class="inline" onsubmit="return confirm('Hapus transaksi {{ $trx->no_referensi }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus" class="inline-flex text-red-600 hover:text-red-600">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $transactions->links() }}</div>
    </div>
</div>
</x-app-layout>