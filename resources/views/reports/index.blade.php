<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Keuangan</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ===== Laporan Resmi (gaya surat pemerintah, per Tahun Anggaran) ===== --}}
            <div class="bg-white rounded-lg shadow p-5 border-l-4" style="border-color: #16325C;">
                <h3 class="font-semibold text-gray-700 mb-1">Laporan Resmi Realisasi Anggaran</h3>
                <p class="text-xs text-gray-500 mb-3">Dokumen formal per Tahun Anggaran, lengkap dengan kop surat & blok tanda tangan pejabat.</p>

                <form method="GET" action="{{ route('reports.official.pdf') }}" class="flex flex-wrap items-end gap-3"
                    x-data="{
                            open: false,
                            selectedYears: [{{ $tahunList->firstWhere('is_active', true)->id ?? $tahunList->first()->id ?? 'null' }}],
                            tahunOptions: @js($tahunList->map(fn($t) => ['id' => $t->id, 'tahun' => $t->tahun, 'aktif' => $t->is_active])->values()),
                            get allSelected() { return this.tahunOptions.length > 0 && this.selectedYears.length === this.tahunOptions.length; },
                            toggleAll() { this.selectedYears = this.allSelected ? [] : this.tahunOptions.map(t => t.id); },
                            toggleYear(id) {
                                this.selectedYears = this.selectedYears.includes(id)
                                    ? this.selectedYears.filter(y => y !== id)
                                    : [...this.selectedYears, id];
                            },
                            get displayText() {
                                if (this.selectedYears.length === 0) return 'Pilih tahun anggaran...';
                                if (this.allSelected) return 'Semua Tahun';
                                return this.tahunOptions.filter(t => this.selectedYears.includes(t.id)).map(t => t.tahun).join(', ');
                            }
                    }" @click.outside="open = false">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Anggaran</label>
                        <div class="relative">
                            <button type="button" @click="open = !open"
                                    class="rounded-md border border-gray-300 text-sm flex justify-between items-center px-3 py-2 bg-white w-64">
                                <span x-text="displayText" class="truncate"></span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" style="display:none;" class="absolute z-10 mt-1 w-64 bg-white border border-gray-200 rounded-md shadow-lg">
                                <label class="flex items-center gap-2 px-3 py-2 text-sm border-b border-gray-100 font-medium cursor-pointer">
                                    <input type="checkbox" :checked="allSelected" @change="toggleAll()" class="rounded border-gray-300">
                                    Semua Tahun
                                </label>
                                <div class="max-h-48 overflow-y-auto">
                                    <template x-for="t in tahunOptions" :key="t.id">
                                        <label class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" name="tahun_anggaran_id[]" :value="t.id" :checked="selectedYears.includes(t.id)" @change="toggleYear(t.id)" class="rounded border-gray-300">
                                            <span x-text="t.tahun"></span>
                                            <span x-show="t.aktif" class="text-xs text-green-600">(Berjalan)</span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="bg-[#16325C] hover:bg-[#0F2340] text-white px-4 py-2 rounded-md text-sm">
                        ⬇ Unduh Laporan Resmi (PDF)
                    </button>
                </form>

                <p class="text-xs text-gray-400 mt-2">
                    Data penandatangan diambil dari
                    <a href="{{ route('settings.pejabat.index') }}" class="underline hover:text-gray-600">Settings → Pejabat Penandatangan</a>.
                    Pilih lebih dari satu tahun untuk laporan gabungan (tiap tahun tampil di halaman terpisah).
                </p>
            </div>

            {{-- ===== Filter & laporan ad-hoc (fleksibel per tanggal/kategori) ===== --}}
            <form method="GET" class="bg-white rounded-lg shadow p-4 grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Anggaran</label>
                    <select name="tahun_anggaran_id" class="w-full rounded-md border-gray-300 text-sm">
                        <option value="">Semua Tahun</option>
                        @foreach ($tahunList as $t)
                            <option value="{{ $t->id }}" {{ request('tahun_anggaran_id') == $t->id ? 'selected' : '' }}>{{ $t->tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="w-full rounded-md border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="w-full rounded-md border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Pagu Anggaran</label>
                    <select name="budget_category_id" class="w-full rounded-md border-gray-300 text-sm">
                        <option value="">Semua</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('budget_category_id') == $cat->id ? 'selected' : '' }}>[{{ $cat->masterKomponen->kode }}] {{ $cat->uraian }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Vendor</label>
                    <select name="vendor_id" class="w-full rounded-md border-gray-300 text-sm">
                        <option value="">Semua</option>
                        @foreach ($vendors as $v)
                            <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->nama_vendor }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Grouping Periode</label>
                    <select name="periode" class="w-full rounded-md border-gray-300 text-sm">
                        <option value="harian" {{ $periode === 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="mingguan" {{ $periode === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan" {{ $periode === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <div class="md:col-span-6 flex justify-between items-center pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Terapkan Filter</button>
                    <div class="space-x-2">
                        <a href="{{ route('reports.export.excel', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">Export Excel</a>
                        <a href="{{ route('reports.export.pdf', request()->query()) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">Export PDF</a>
                    </div>
                </div>
            </form>

            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-500">Total Pengeluaran (sesuai filter)</p>
                <p class="text-xl font-bold text-red-600">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</p>
            </div>

            @foreach ($grouped as $label => $items)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 font-medium text-sm text-gray-700">{{ $label }}</div>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y">
                            @foreach ($items as $trx)
                                <tr>
                                    <td class="px-4 py-2 font-mono text-xs">{{ $trx->no_referensi }}</td>
                                    <td class="px-4 py-2">{{ $trx->tanggal->format('d-m-Y') }}</td>
                                    <td class="px-4 py-2">[{{ $trx->budgetCategory->masterKomponen->kode ?? '-' }}] {{ $trx->budgetCategory->uraian ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $trx->vendor->nama_vendor ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $trx->uraian }}</td>
                                    <td class="px-4 py-2 text-right font-medium text-red-600">
                                        Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            @if ($grouped->isEmpty())
                <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">Tidak ada data untuk filter ini.</div>
            @endif
        </div>
    </div>
</x-app-layout>
