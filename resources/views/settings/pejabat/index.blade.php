<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Settings — Pejabat Penandatangan Laporan</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-50 text-green-700 text-sm px-4 py-3 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif

            <div class="bg-blue-50 text-blue-700 text-sm px-4 py-3 rounded-md border border-blue-200">
                Nama & jabatan di sini otomatis muncul di blok tanda tangan pada Laporan Resmi (PDF)
                Pagu Anggaran. Hanya satu pejabat yang bisa "Aktif" dalam satu waktu.
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="font-semibold text-gray-700 mb-3">Tambah Pejabat Baru</h3>
                @if ($errors->any())
                    <div class="mb-3 bg-red-50 text-red-700 text-sm px-4 py-2 rounded-md border border-red-200">
                        <ul class="list-disc list-inside">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
                    </div>
                @endif
                <form action="{{ route('settings.pejabat.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Lengkap (dengan gelar)</label>
                        <input type="text" name="nama" required placeholder="Contoh: Eko Nugroho, S.Psi." class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jabatan</label>
                        <input type="text" name="jabatan" required placeholder="Contoh: Kepala UPT BKN Pangkalpinang" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">NIP</label>
                        <input type="text" name="nip" placeholder="Contoh: 198501012010011001" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Tambah</button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Jabatan</th>
                            <th class="px-4 py-3">NIP</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($pejabatList as $p)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $p->nama }}</td>
                                <td class="px-4 py-3">{{ $p->jabatan }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $p->nip ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs {{ $p->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 space-x-2">
                                    <details class="inline-block">
                                        <summary class="text-xs text-blue-600 cursor-pointer inline">Edit</summary>
                                        <form action="{{ route('settings.pejabat.update', $p) }}" method="POST" class="mt-2 space-y-2 w-64">
                                            @csrf @method('PUT')
                                            <input type="text" name="nama" value="{{ $p->nama }}" required class="w-full text-xs rounded-md border-gray-300">
                                            <input type="text" name="jabatan" value="{{ $p->jabatan }}" required class="w-full text-xs rounded-md border-gray-300">
                                            <input type="text" name="nip" value="{{ $p->nip }}" class="w-full text-xs rounded-md border-gray-300">
                                            <button type="submit" class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-md">Simpan</button>
                                        </form>
                                    </details>
                                    @unless ($p->is_active)
                                        <form action="{{ route('settings.pejabat.aktifkan', $p) }}" method="POST" class="inline" onsubmit="return confirm('Jadikan {{ $p->nama }} sebagai penandatangan aktif?')">
                                            @csrf @method('PUT')
                                            <button type="submit" class="text-xs text-green-600 hover:underline">Aktifkan</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada data pejabat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
