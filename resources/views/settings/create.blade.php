<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Pagu Anggaran</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 text-red-700 text-sm px-4 py-3 rounded-md border border-red-200">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('budget-categories.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Kategori</label>
                        <input type="text" name="kode_kategori" value="{{ old('kode_kategori') }}" required placeholder="Contoh: OPS-2026"
                               class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                        <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" required placeholder="Contoh: Operasional Kantor"
                               class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Anggaran</label>
                        <input type="number" name="tahun_anggaran" value="{{ old('tahun_anggaran', date('Y')) }}" required min="2020" max="2100"
                               class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pagu Anggaran (Rp)</label>
                        <input type="number" name="pagu_anggaran" value="{{ old('pagu_anggaran') }}" required min="1"
                               class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full rounded-md border-gray-300">{{ old('keterangan') }}</textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('budget-categories.index') }}" class="px-4 py-2 rounded-md text-sm text-gray-600 hover:bg-gray-100">Batal</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
