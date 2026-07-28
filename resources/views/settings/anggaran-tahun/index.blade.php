<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Settings — Tahun Anggaran</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-50 text-green-700 text-sm px-4 py-3 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 text-red-700 text-sm px-4 py-3 rounded-md border border-red-200">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="font-semibold text-gray-700 mb-3">Tambah Tahun Anggaran Baru</h3>
                @if ($errors->any())
                    <div class="mb-3 bg-red-50 text-red-700 text-sm px-4 py-2 rounded-md border border-red-200">
                        <ul class="list-disc list-inside">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
                    </div>
                @endif
                <form action="{{ route('settings.anggaran-tahun.store') }}" method="POST" class="flex gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                        <input type="number" name="tahun" required min="2020" max="2100" value="{{ old('tahun', date('Y')) }}" class="rounded-md border-gray-300 text-sm">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Tambah</button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Tahun</th>
                            <th class="px-4 py-3">Jumlah Pagu Dibuat</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($tahunList as $t)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $t->tahun }}</td>
                                <td class="px-4 py-3">{{ $t->budget_categories_count }} kategori</td>
                                <td class="px-4 py-3">
                                    @if ($t->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Aktif (berjalan)</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 space-x-3">
                                    @unless ($t->is_active)
                                        <form action="{{ route('settings.anggaran-tahun.aktifkan', $t) }}" method="POST" class="inline" onsubmit="return confirm('Jadikan {{ $t->tahun }} sebagai tahun anggaran yang berjalan?')">
                                            @csrf @method('PUT')
                                            <button type="submit" class="text-blue-600 hover:underline text-xs">Aktifkan</button>
                                        </form>
                                    @endunless
                                    <a href="{{ route('budget-categories.index', ['tahun_anggaran_id' => $t->id]) }}" class="text-gray-600 hover:underline text-xs">Lihat Pagu</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada tahun anggaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
