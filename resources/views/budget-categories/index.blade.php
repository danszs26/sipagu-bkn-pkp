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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($categories as $cat)
                        @php $persen = $cat->persentase_terpakai; @endphp
                        <div class="bg-white rounded-lg shadow p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="inline-block bg-gray-100 text-gray-600 text-xs font-mono font-semibold px-2 py-0.5 rounded mb-1">
                                        {{ $cat->masterKomponen->kode }}
                                    </span>
                                    <p class="font-semibold text-gray-800">{{ $cat->uraian }}</p>
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
                                    <select name="master_komponen_id" required class="w-full text-sm rounded-md border-gray-300">
                                        @foreach (\App\Models\MasterKomponen::where('is_active', true)->orderBy('kode')->get() as $k)
                                            <option value="{{ $k->id }}" {{ $cat->master_komponen_id === $k->id ? 'selected' : '' }}>{{ $k->kode }} — {{ $k->nama_komponen }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="uraian" value="{{ $cat->uraian }}" required class="w-full text-sm rounded-md border-gray-300">
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

                @if ($categories->isEmpty())
                    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
                        Belum ada pagu anggaran untuk tahun {{ $tahunTerpilih->tahun }}.
                    </div>
                @endif
            @else
                <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
                    Belum ada Tahun Anggaran. Buat dulu di menu Settings → Tahun Anggaran.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
