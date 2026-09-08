<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('transaction'));
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'budget_category_id' => ['required', 'exists:budget_categories,id'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'vendor_baru' => ['nullable', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'min:1'],
            'uraian' => ['required', 'string', 'min:5', 'max:500'],
            'bukti_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
