<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Pagu Anggaran — Tahun {{ $tahun->tahun }}</h2>
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
                    <input type="hidden" name="tahun_anggaran_id" value="{{ $tahun->id }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Anggaran</label>
                        <input type="text" value="{{ $tahun->tahun }}" disabled class="w-full rounded-md border-gray-300 bg-gray-100 text-gray-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Komponen/Akun</label>
                        <select name="master_komponen_id" required class="w-full rounded-md border-gray-300">
                            <option value="">-- Pilih Komponen --</option>
                            @foreach ($komponenList as $k)
                                <option value="{{ $k->id }}" {{ old('master_komponen_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->kode }} — {{ $k->nama_komponen }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Belum ada komponennya? Tambah dulu di Settings → Master Komponen.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Uraian / Detail</label>
                        <input type="text" name="uraian" value="{{ old('uraian') }}" required placeholder="Contoh: Belanja Keperluan Kantor"
                               class="w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pagu Anggaran (Rp)</label>
                        <input type="number" name="pagu_anggaran" value="{{ old('pagu_anggaran') }}" required min="1"
                               class="w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (opsional)</label>
                        <textarea name="keterangan" rows="2" class="w-full rounded-md border-gray-300">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('budget-categories.index', ['tahun_anggaran_id' => $tahun->id]) }}" class="px-4 py-2 rounded-md text-sm text-gray-600 hover:bg-gray-100">Batal</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
