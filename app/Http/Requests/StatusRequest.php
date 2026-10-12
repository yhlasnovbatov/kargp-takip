<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StatusRequest extends FormRequest
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
        if ($this->filled('name') && !$this->filled('slug')) {
            $this->merge([
                'slug' => Str::slug($this->name),
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
        $statusId = $this->route('status') ? $this->route('status')->id ?? $this->route('status') : null;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('statuses', 'name')->ignore($statusId),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('statuses', 'slug')->ignore($statusId),
            ],
            'color' => ['nullable', 'string', 'max:30'],
            'badge_class' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Durum adı alanı zorunludur.',
            'name.unique' => 'Bu durum adı zaten mevcut.',
        ];
    }
}
