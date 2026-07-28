<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Transaction::class);
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'budget_category_id' => ['required', 'exists:budget_categories,id'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'vendor_baru' => ['nullable', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'min:1000', 'multiple_of:100'],
            'uraian' => ['required', 'string', 'min:5', 'max:500'],
            'bukti_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'idempotency_token' => ['required', 'uuid', 'unique:transactions,idempotency_token'],
        ];
    }

    public function messages(): array
    {
        return [
            'bukti_file.required' => 'Bukti fisik (kuitansi/nota/invoice) wajib diunggah.',
            'nominal.min' => 'Nominal minimal Rp1.000.',
            'nominal.multiple_of' => 'Nominal harus kelipatan Rp100 (sesuai pecahan rupiah).',
            'idempotency_token.unique' => 'Transaksi ini sudah pernah dikirim sebelumnya (double submit terdeteksi).',
        ];
    }
}
