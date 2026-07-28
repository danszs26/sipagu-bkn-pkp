<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pagu Anggaran</h2>
            @can('create', App\Models\BudgetCategory::class)
                <a href="{{ route('budget-categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md">+ Tambah Kategori</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-50 text-green-700 text-sm px-4 py-3 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($categories as $cat)
                    @php $persen = $cat->persentase_terpakai; @endphp
                    <div class="bg-white rounded-lg shadow p-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $cat->nama_kategori }}</p>
                                <p class="text-xs text-gray-400">{{ $cat->kode_kategori }} · Tahun {{ $cat->tahun_anggaran }}</p>
                            </div>
                            @if (!$cat->is_active)
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">Nonaktif</span>
                            @endif
                        </div>

                        <div class="mt-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span>Terpakai: Rp {{ number_format($cat->total_terpakai, 0, ',', '.') }}</span>
                                <span class="text-gray-500">{{ $persen }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="{{ $persen >= 90 ? 'bg-red-500' : ($persen >= 70 ? 'bg-yellow-500' : 'bg-green-500') }} h-2.5 rounded-full" style="width: {{ min($persen, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pagu: Rp {{ number_format($cat->pagu_anggaran, 0, ',', '.') }} · Sisa: Rp {{ number_format($cat->sisa_anggaran, 0, ',', '.') }}</p>
                        </div>

                        @can('update', $cat)
                        <details class="mt-3">
                            <summary class="text-xs text-blue-600 cursor-pointer">Edit</summary>
                            <form action="{{ route('budget-categories.update', $cat) }}" method="POST" class="mt-2 space-y-2">
                                @csrf @method('PUT')
                                <input type="text" name="nama_kategori" value="{{ $cat->nama_kategori }}" required class="w-full text-sm rounded-md border-gray-300">
                                <input type="number" name="pagu_anggaran" value="{{ $cat->pagu_anggaran }}" required min="1" class="w-full text-sm rounded-md border-gray-300">
                                <textarea name="keterangan" placeholder="Keterangan" class="w-full text-sm rounded-md border-gray-300">{{ $cat->keterangan }}</textarea>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="is_active" value="1" {{ $cat->is_active ? 'checked' : '' }}> Aktif
                                </label>
                                <button type="submit" class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-md">Simpan</button>
                            </form>
                        </details>
                        @endcan
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
