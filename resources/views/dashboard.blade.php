<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (!$tahunAktif)
                <div class="bg-yellow-50 text-yellow-700 text-sm px-4 py-3 rounded-md border border-yellow-200">
                    Belum ada Tahun Anggaran yang diaktifkan. Buka <strong>Settings → Tahun Anggaran</strong> untuk mengaktifkan salah satu.
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Total Pagu {{ $tahunAktif->tahun ?? '' }}</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalPagu, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-5 border-l-4 border-red-500">
                    <p class="text-sm text-gray-500">Total Terpakai</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">Rp {{ number_format($totalTerpakai, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Sisa Pagu</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($sisaPagu, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="font-semibold text-gray-700 mb-3">Pengeluaran 30 Hari Terakhir</h3>
                <canvas id="pengeluaranChart" height="90"></canvas>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="font-semibold text-gray-700 mb-4">Penggunaan Pagu per Komponen</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[700px] overflow-y-auto pr-2">
                    @forelse ($categories as $cat)
                        @php $persen = $cat->persentase_terpakai; @endphp

                        <div class="bg-gray-50 rounded-lg border p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="inline-block bg-gray-100 text-gray-600 text-xs font-mono font-semibold px-2 py-0.5 rounded mb-1">
                                        {{ $cat->masterKomponen->kode }}
                                    </span>

                                    <p class="font-semibold text-gray-800">
                                        {{ $cat->uraian }}
                                    </p>
                                </div>

                                @if (!$cat->is_active)
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">
                                        Nonaktif
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>
                                        Terpakai:
                                        Rp {{ number_format($cat->total_terpakai, 0, ',', '.') }}
                                    </span>

                                    <span class="text-gray-500">
                                        {{ $persen }}%
                                    </span>
                                </div>

                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div
                                        class="{{ $persen >= 90 ? 'bg-red-500' : ($persen >= 70 ? 'bg-yellow-500' : 'bg-green-500') }} h-2.5 rounded-full"
                                        style="width: {{ min($persen, 100) }}%">
                                    </div>
                                </div>

                                <p class="text-xs text-gray-500 mt-1">
                                    Pagu:
                                    Rp {{ number_format($cat->pagu_anggaran, 0, ',', '.') }}
                                    ·
                                    Sisa:
                                    Rp {{ number_format($cat->sisa_anggaran, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                    @empty
                        <div class="col-span-full text-center text-gray-400 py-8">
                            Belum ada pagu anggaran untuk tahun ini.
                        </div>
                    @endforelse
                </div>
            </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <h3 class="font-semibold text-gray-700 mb-3">Transaksi Terbaru</h3>
                    <div class="divide-y">
                        @forelse ($transaksiTerbaru as $trx)
                            <div class="py-2 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ $trx->uraian }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $trx->no_referensi }} · {{ $trx->tanggal->format('d M Y') }}
                                        @if ($trx->vendor) · {{ $trx->vendor->nama_vendor }} @endif
                                        · {{ $trx->creator->name }}
                                    </p>
                                </div>
                                <span class="text-sm font-semibold text-red-600">
                                    - Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">Belum ada transaksi.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const rawData = @json($pengeluaran30hari);
        const labels = rawData.map(r => r.tanggal);
        const data = rawData.map(r => parseFloat(r.total));

        new Chart(document.getElementById('pengeluaranChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Pengeluaran', data: data, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.1)', tension: 0.3, fill: true },
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } } }
            }
        });
    </script>
    @endpush
</x-app-layout>
