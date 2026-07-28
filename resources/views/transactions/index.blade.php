<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transaksi</h2>
            <a href="{{ route('transactions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md">+ Tambah Transaksi</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-50 text-green-700 text-sm px-4 py-3 rounded-md border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-left">
                        <tr>
                            <th class="px-4 py-3">No Referensi</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Komponen</th>
                            <th class="px-4 py-3">Pagu / Uraian</th>
                            <th class="px-4 py-3">Vendor</th>
                            <th class="px-4 py-3">Uraian Transaksi</th>
                            <th class="px-4 py-3 text-right">Nominal</th>
                            <th class="px-4 py-3">Diinput oleh</th>
                            <th class="px-4 py-3">Bukti</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($transactions as $trx)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs">{{ $trx->no_referensi }}</td>
                                <td class="px-4 py-3">{{ $trx->tanggal->format('d-m-Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-gray-100 text-gray-600 text-xs font-mono px-1.5 py-0.5 rounded">{{ $trx->budgetCategory->masterKomponen->kode ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate">{{ $trx->budgetCategory->uraian ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $trx->vendor->nama_vendor ?? '—' }}</td>
                                <td class="px-4 py-3 max-w-xs truncate">{{ $trx->uraian }}</td>
                                <td class="px-4 py-3 text-right font-medium text-red-600">Rp {{ number_format($trx->nominal, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $trx->creator->name }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('transactions.bukti', $trx) }}" target="_blank" class="text-blue-600 hover:underline text-xs">Lihat</a>
                                </td>
                                <td class="px-4 py-3 space-x-2 whitespace-nowrap">
                                    @can('update', $trx)
                                        <a href="{{ route('transactions.edit', $trx) }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                                    @endcan
                                    @can('delete', $trx)
                                        <form action="{{ route('transactions.destroy', $trx) }}" method="POST" class="inline" onsubmit="return confirm('Hapus transaksi {{ $trx->no_referensi }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-xs">Hapus</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $transactions->links() }}</div>
        </div>
    </div>
</x-app-layout>
