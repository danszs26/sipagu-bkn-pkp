<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Transaksi — {{ $transaction->no_referensi }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
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

                <form action="{{ route('transactions.update', $transaction) }}" method="POST" enctype="multipart/form-data" id="trxForm">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $transaction->tanggal->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pagu Anggaran</label>
                        <select name="budget_category_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('budget_category_id', $transaction->budget_category_id) == $cat->id ? 'selected' : '' }}>
                                    [{{ $cat->masterKomponen->kode }}] {{ $cat->uraian }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4" x-data="{
                            open: false,
                            vendorId: '{{ old('vendor_id', $transaction->vendor_id ?? '') }}',
                            vendorBaru: @js(old('vendor_baru', '')),
                            vendors: @js($vendors->map(fn($v) => ['id' => $v->id, 'nama' => $v->nama_vendor])->values()),
                            get filteredVendors() {
                                if (!this.vendorBaru) return this.vendors;
                                const q = this.vendorBaru.toLowerCase();
                                return this.vendors.filter(v => v.nama.toLowerCase().includes(q));
                            },
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
                                    <template x-for="v in filteredVendors" :key="v.id">
                                        <button type="button" @click="pilihVendor(v)" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50" x-text="v.nama"></button>
                                    </template>
                                    <p x-show="vendorBaru && filteredVendors.length === 0" style="display:none;" class="px-3 py-2 text-xs text-gray-400 italic">
                                        Tidak ditemukan — akan dibuat vendor baru "<span x-text="vendorBaru"></span>" saat disimpan.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Ketik di kotak paling atas untuk vendor baru (otomatis tersimpan), atau klik salah satu nama di daftar bawahnya.</p>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                        <input type="text" id="nominal_display" inputmode="numeric" required
                               value="{{ number_format($transaction->nominal, 0, ',', '.') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <input type="hidden" name="nominal" id="nominal_value" value="{{ $transaction->nominal }}">
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Uraian / Deskripsi</label>
                        <textarea name="uraian" rows="3" required minlength="5" maxlength="500"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('uraian', $transaction->uraian) }}</textarea>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Fisik</label>
                        <p class="text-xs text-gray-500 mb-1">
                            File saat ini: <a href="{{ route('transactions.bukti', $transaction) }}" target="_blank" class="text-blue-600 hover:underline">{{ $transaction->bukti_file_original_name }}</a>
                        </p>
                        <input type="file" name="bukti_file" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full text-sm text-gray-600 border border-gray-300 rounded-md p-2">
                        <p class="text-xs text-gray-400 mt-1">Biarkan kosong jika tidak ingin mengganti bukti.</p>
                    </div>

                    <div class="mt-3 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs px-3 py-2 rounded-md">
                        ⚠️ Setiap perubahan pada transaksi ini akan tercatat di Audit Log (siapa, apa yang diubah, dan kapan).
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('transactions.index') }}" class="px-4 py-2 rounded-md text-sm text-gray-600 hover:bg-gray-100">Batal</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md text-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const displayInput = document.getElementById('nominal_display');
        const hiddenInput = document.getElementById('nominal_value');
        displayInput.addEventListener('input', function () {
            let raw = this.value.replace(/\D/g, '');
            hiddenInput.value = raw;
            this.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
        });
    </script>
    @endpush
</x-app-layout>
