<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        // Sipariş numarası boş ise otomatik oluştur
        if (!$this->filled('order_number')) {
            $this->merge([
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $orderId = $this->route('order') ? $this->route('order')->id ?? $this->route('order') : null;

        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'status_id' => ['required', 'exists:statuses,id'],
            'order_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('orders', 'order_number')->ignore($orderId),
            ],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Müşteri seçimi zorunludur.',
            'customer_id.exists' => 'Seçilen müşteri sistemde bulunamadı.',
            'status_id.required' => 'Durum seçimi zorunludur.',
            'status_id.exists' => 'Seçilen durum sistemde bulunamadı.',
            'order_number.required' => 'Sipariş numarası zorunludur.',
            'order_number.unique' => 'Bu sipariş numarası zaten kullanılıyor.',
            'total_amount.required' => 'Sipariş tutarı zorunludur.',
            'total_amount.numeric' => 'Sipariş tutarı geçerli bir sayı olmalıdır.',
        ];
    }
}
