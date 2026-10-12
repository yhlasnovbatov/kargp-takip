<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShipmentRequest extends FormRequest
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
        // Takip numarası girilmediyse otomatik üret
        if (!$this->filled('tracking_number')) {
            $prefix = 'YK';
            $rand = rand(10000000, 99999999);
            $this->merge([
                'tracking_number' => "{$prefix}-{$rand}",
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
        $shipmentId = $this->route('shipment') ? $this->route('shipment')->id ?? $this->route('shipment') : null;

        return [
            'order_id' => [
                'required',
                'exists:orders,id',
                Rule::unique('shipments', 'order_id')->ignore($shipmentId),
            ],
            'company' => ['required', 'string', 'max:100'],
            'tracking_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('shipments', 'tracking_number')->ignore($shipmentId),
            ],
            'shipped_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
            'current_status_desc' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'order_id.required' => 'Sipariş seçimi zorunludur.',
            'order_id.exists' => 'Seçilen sipariş bulunamadı.',
            'order_id.unique' => 'Bu siparişe ait zaten bir kargo kaydı bulunmaktadır.',
            'company.required' => 'Kargo firması alanı zorunludur.',
            'tracking_number.required' => 'Takip numarası zorunludur.',
            'tracking_number.unique' => 'Bu takip numarası zaten kullanılmaktadır.',
        ];
    }
}
