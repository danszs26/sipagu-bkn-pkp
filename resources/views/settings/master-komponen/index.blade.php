<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Settings — Master Komponen/Akun</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-50 text-green-700 text-sm px-4 py-3 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="font-semibold text-gray-700 mb-3">Tambah Master Komponen Baru</h3>
                @if ($errors->any())
                    <div class="mb-3 bg-red-50 text-red-700 text-sm px-4 py-2 rounded-md border border-red-200">
                        <ul class="list-disc list-inside">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
                    </div>
                @endif
                <form action="{{ route('settings.master-komponen.store') }}" method="POST" class="flex gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode</label>
                        <input type="text" name="kode" required maxlength="10" placeholder="Contoh: S" class="rounded-md border-gray-300 text-sm w-24">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Komponen (opsional)</label>
                        <input type="text" name="nama_komponen" placeholder="Contoh: Layanan Keamanan" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Tambah</button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Nama Komponen</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($komponenList as $k)
                            <tr>
                                <td class="px-4 py-3 font-mono font-semibold">{{ $k->kode }}</td>
                                <td class="px-4 py-3">{{ $k->nama_komponen ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs {{ $k->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $k->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('settings.master-komponen.update', $k) }}" method="POST" class="flex items-center gap-2">
                                        @csrf @method('PUT')
                                        <input type="text" name="nama_komponen" value="{{ $k->nama_komponen }}" class="text-xs rounded-md border-gray-300">
                                        <label class="flex items-center gap-1 text-xs">
                                            <input type="checkbox" name="is_active" value="1" {{ $k->is_active ? 'checked' : '' }}> Aktif
                                        </label>
                                        <button type="submit" class="text-blue-600 hover:underline text-xs">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada master komponen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
