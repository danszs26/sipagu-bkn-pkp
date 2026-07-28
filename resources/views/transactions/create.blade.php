<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Transaksi</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">

                @if ($categories->isEmpty())
                    <div class="bg-yellow-50 text-yellow-700 text-sm px-4 py-3 rounded-md border border-yellow-200 mb-4">
                        Belum ada pagu anggaran untuk tahun anggaran yang sedang aktif. Buat dulu lewat menu <strong>Pagu Anggaran</strong>.
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 text-red-700 text-sm px-4 py-3 rounded-md border border-red-200">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" id="trxForm">
                    @csrf
                    <input type="hidden" name="idempotency_token" id="idempotency_token">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pagu Anggaran</label>
                        <select name="budget_category_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih Pagu Anggaran --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('budget_category_id') == $cat->id ? 'selected' : '' }}>
                                    [{{ $cat->masterKomponen->kode }}] {{ $cat->uraian }} — Sisa: Rp {{ number_format($cat->sisa_anggaran, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4" x-data="{
                            open: false,
                            vendorId: '{{ old('vendor_id') }}',
                            vendorBaru: @js(old('vendor_baru', '')),
                            vendors: @js($vendors->map(fn($v) => ['id' => $v->id, 'nama' => $v->nama_vendor])->values()),
                            get displayText() {
                                if (this.vendorBaru) return this.vendorBaru;
                                const found = this.vendors.find(v => v.id == this.vendorId);
                                return found ? found.nama : '';
                            },
                            pilihVendor(v) { this.vendorId = v.id; this.vendorBaru = ''; this.open = false; },
                            pilihKosong() { this.vendorId = ''; this.vendorBaru = ''; this.open = false; },
                        }" @click.outside="open = false">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vendor (opsional)</label>

                        <input type="hidden" name="vendor_id" :value="vendorId">
                        <input type="hidden" name="vendor_baru" :value="vendorBaru">

                        <div class="relative">
                            <button type="button" @click="open = !open"
                                    class="w-full text-left rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 flex justify-between items-center px-3 py-2 bg-white">
                                <span x-text="displayText || '-- Tanpa Vendor / Pilih atau ketik vendor baru --'" :class="displayText ? 'text-gray-900' : 'text-gray-400'"></span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" style="display: none;" class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg">
                                <div class="p-2 border-b border-gray-100">
                                    <input type="text" placeholder="Ketik nama vendor baru..." x-model="vendorBaru" @input="vendorId = ''"
                                        class="w-full text-sm rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div class="max-h-48 overflow-y-auto">
                                    <button type="button" @click="pilihKosong()" class="w-full text-left px-3 py-2 text-sm text-gray-500 hover:bg-gray-50">
                                        -- Tanpa Vendor --
                                    </button>
                                    <template x-for="v in vendors" :key="v.id">
                                        <button type="button" @click="pilihVendor(v)" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50" x-text="v.nama"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Ketik di kotak paling atas untuk vendor baru (otomatis tersimpan), atau klik salah satu nama di daftar bawahnya.</p>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                        <input type="text" id="nominal_display" inputmode="numeric" placeholder="0" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <input type="hidden" name="nominal" id="nominal_value">
                        <p class="text-xs text-gray-400 mt-1">Nominal otomatis mengikuti pecahan rupiah (kelipatan Rp100), minimal Rp1.000.</p>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Uraian / Deskripsi</label>
                        <textarea name="uraian" rows="3" required minlength="5" maxlength="500"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('uraian') }}</textarea>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Fisik (Kuitansi/Nota/Invoice) <span class="text-red-500">*wajib</span></label>
                        <input type="file" name="bukti_file" accept=".jpg,.jpeg,.png,.pdf" required
                               class="w-full text-sm text-gray-600 border border-gray-300 rounded-md p-2">
                        <p class="text-xs text-gray-400 mt-1">Format JPG/PNG/PDF, maksimal 5MB.</p>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('transactions.index') }}" class="px-4 py-2 rounded-md text-sm text-gray-600 hover:bg-gray-100">Batal</a>
                        <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md text-sm">
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('idempotency_token').value = crypto.randomUUID();

        const displayInput = document.getElementById('nominal_display');
        const hiddenInput = document.getElementById('nominal_value');

        displayInput.addEventListener('input', function () {
            let raw = this.value.replace(/\D/g, '');
            hiddenInput.value = raw;
            this.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
        });

        document.getElementById('trxForm').addEventListener('submit', function () {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').innerText = 'Menyimpan...';
        });
    </script>
    @endpush
</x-app-layout>
