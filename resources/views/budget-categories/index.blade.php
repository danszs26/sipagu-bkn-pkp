<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pagu Anggaran</h2>
            @can('create', App\Models\BudgetCategory::class)
                @if ($tahunTerpilih)
                    <a href="{{ route('budget-categories.create', ['tahun_anggaran_id' => $tahunTerpilih->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md">+ Tambah Pagu</a>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-50 text-green-700 text-sm px-4 py-3 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 text-red-700 text-sm px-4 py-3 rounded-md border border-red-200">{{ session('error') }}</div>
            @endif

            {{-- Selector tahun anggaran --}}
            <form method="GET" class="bg-white rounded-lg shadow p-4 flex items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Anggaran</label>
                    <select name="tahun_anggaran_id" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                        @forelse ($tahunList as $t)
                            <option value="{{ $t->id }}" {{ $tahunTerpilih && $tahunTerpilih->id === $t->id ? 'selected' : '' }}>
                                {{ $t->tahun }} {{ $t->is_active ? '(Berjalan)' : '' }}
                            </option>
                        @empty
                            <option value="">Belum ada tahun anggaran</option>
                        @endforelse
                    </select>
                </div>
                @if ($tahunList->isEmpty())
                    <a href="{{ route('settings.anggaran-tahun.index') }}" class="text-sm text-blue-600 hover:underline">+ Buat Tahun Anggaran dulu di Settings</a>
                @endif
            </form>

            @can('create', App\Models\BudgetCategory::class)
            @if ($tahunTerpilih && $tahunList->count() > 1)
                <details class="bg-white rounded-lg shadow p-4">
                    <summary class="text-sm text-blue-600 cursor-pointer font-medium">📋 Salin komponen/uraian dari Tahun Anggaran lain</summary>
                    <form action="{{ route('budget-categories.copy') }}" method="POST" class="mt-3 flex flex-wrap items-end gap-3"
                          onsubmit="return confirm('Salin semua komponen & uraian dari tahun yang dipilih ke tahun {{ $tahunTerpilih->tahun }}? Nilai pagu akan diisi placeholder Rp1, wajib diedit manual setelahnya.')">
                        @csrf
                        <input type="hidden" name="ke_tahun_anggaran_id" value="{{ $tahunTerpilih->id }}">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Salin dari Tahun</label>
                            <select name="dari_tahun_anggaran_id" required class="rounded-md border-gray-300 text-sm">
                                <option value="">-- Pilih Tahun Sumber --</option>
                                @foreach ($tahunList as $t)
                                    @if ($t->id !== $tahunTerpilih->id)
                                        <option value="{{ $t->id }}">{{ $t->tahun }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-md text-sm">Salin Sekarang</button>
                        <p class="text-xs text-gray-400 w-full mt-1">Hanya komponen & uraian yang disalin. Nilai pagu tetap harus diisi manual lewat Edit setelah disalin. Item yang sudah ada (sama komponen & uraiannya) di tahun tujuan tidak akan digandakan.</p>
                    </form>
                </details>
            @endif
            @endcan

            @if ($tahunTerpilih)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="px-4 py-2">Kode</th>
                                <th class="px-4 py-2">Nama Komponen</th>
                                <th class="px-4 py-2 text-center">Jumlah Item</th>
                                <th class="px-4 py-2 text-right">Total Pagu</th>
                                <th class="px-4 py-2 text-right">Total Terpakai</th>
                                <th class="px-4 py-2 text-right">Sisa</th>
                                <th class="px-4 py-2 text-center">% Serapan</th>
                            </tr>
                        </thead>
                        @forelse ($perKomponen as $row)
                            @php
                                $sisa = $row['total_pagu'] - $row['total_terpakai'];
                                $persen = $row['total_pagu'] > 0 ? round(($row['total_terpakai'] / $row['total_pagu']) * 100, 1) : 0;
                            @endphp
                            <tbody x-data="{ open: false }" class="divide-y">
                                <tr @click="open = !open" class="cursor-pointer hover:bg-gray-50">
                                    <td class="px-4 py-2 font-mono font-semibold">
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 flex-shrink-0"
                                                 :class="open ? 'rotate-180' : ''"
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                            {{ $row['komponen']->kode ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">{{ $row['komponen']->nama_komponen ?? '-' }}</td>
                                    <td class="px-4 py-2 text-center">{{ $row['items']->count() }}</td>
                                    <td class="px-4 py-2 text-right">Rp {{ number_format($row['total_pagu'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right text-red-600">Rp {{ number_format($row['total_terpakai'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right text-green-600">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-center">{{ $persen }}%</td>
                                </tr>
                                <tr x-show="open" style="display: none;">
                                    <td colspan="7" class="px-0 py-0 bg-gray-50">
                                        <div class="px-6 py-3">
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Rincian Sub-Komponen / Uraian</p>
                                            <table class="w-full text-xs">
                                                <thead>
                                                    <tr class="text-gray-500 border-b border-gray-200">
                                                        <th class="text-left py-1.5 font-medium">Uraian</th>
                                                        <th class="text-right py-1.5 font-medium">Pagu</th>
                                                        <th class="text-right py-1.5 font-medium">Terpakai</th>
                                                        <th class="text-right py-1.5 font-medium">Sisa</th>
                                                        <th class="text-center py-1.5 font-medium w-20">% Serapan</th>
                                                        <th class="text-center py-1.5 font-medium w-16">Status</th>
                                                        @can('update', $row['items']->first())
                                                            <th class="text-center py-1.5 font-medium w-16">Aksi</th>
                                                        @endcan
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    @foreach ($row['items'] as $item)
                                                        @php
                                                            $sisaItem = $item->pagu_anggaran - $item->total_terpakai;
                                                            $persenItem = $item->pagu_anggaran > 0 ? round(($item->total_terpakai / $item->pagu_anggaran) * 100, 1) : 0;
                                                        @endphp
                                                        <tr>
                                                            <td class="py-2 pr-2">{{ $item->uraian }}</td>
                                                            <td class="py-2 text-right">Rp {{ number_format($item->pagu_anggaran, 0, ',', '.') }}</td>
                                                            <td class="py-2 text-right text-red-600">Rp {{ number_format($item->total_terpakai, 0, ',', '.') }}</td>
                                                            <td class="py-2 text-right text-green-600">Rp {{ number_format($sisaItem, 0, ',', '.') }}</td>
                                                            <td class="py-2 text-center">{{ $persenItem }}%</td>
                                                            <td class="py-2 text-center">
                                                                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                                                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                                                </span>
                                                            </td>
                                                            @can('update', $item)
                                                                <td class="py-2 text-center">
                                                                    <details>
                                                                        <summary class="text-blue-600 cursor-pointer text-xs">Edit</summary>
                                                                        <form action="{{ route('budget-categories.update', $item) }}" method="POST" class="mt-2 space-y-2 text-left bg-white border border-gray-200 rounded-md p-3" style="min-width:220px;">
                                                                            @csrf @method('PUT')
                                                                            <select name="master_komponen_id" required class="w-full text-xs rounded-md border-gray-300">
                                                                                @foreach ($komponenList as $k)
                                                                                    <option value="{{ $k->id }}" {{ $item->master_komponen_id === $k->id ? 'selected' : '' }}>{{ $k->kode }} — {{ $k->nama_komponen }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <input type="text" name="uraian" value="{{ $item->uraian }}" required class="w-full text-xs rounded-md border-gray-300">
                                                                            <input type="number" name="pagu_anggaran" value="{{ $item->pagu_anggaran }}" required min="1" class="w-full text-xs rounded-md border-gray-300">
                                                                            <textarea name="keterangan" placeholder="Keterangan" class="w-full text-xs rounded-md border-gray-300">{{ $item->keterangan }}</textarea>
                                                                            <label class="flex items-center gap-2 text-xs">
                                                                                <input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}> Aktif
                                                                            </label>
                                                                            <button type="submit" class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-md w-full">Simpan</button>
                                                                        </form>
                                                                    </details>
                                                                </td>
                                                            @endcan
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        @empty
                            <tbody>
                                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada pagu anggaran untuk tahun {{ $tahunTerpilih->tahun }}.</td></tr>
                            </tbody>
                        @endforelse
                        @if ($perKomponen->isNotEmpty())
                            <tfoot>
                                <tr class="bg-gray-50 font-semibold">
                                    <td class="px-4 py-2" colspan="3">Total Keseluruhan</td>
                                    <td class="px-4 py-2 text-right">Rp {{ number_format($perKomponen->sum('total_pagu'), 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right text-red-600">Rp {{ number_format($perKomponen->sum('total_terpakai'), 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right text-green-600">Rp {{ number_format($perKomponen->sum('total_pagu') - $perKomponen->sum('total_terpakai'), 0, ',', '.') }}</td>
                                    <td class="px-4 py-2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
                    Belum ada Tahun Anggaran. Buat dulu di menu Settings → Tahun Anggaran.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>