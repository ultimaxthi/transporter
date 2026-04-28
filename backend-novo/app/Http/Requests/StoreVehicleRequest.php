<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ✅ corrigido - conectar com policy depois
    }

    public function rules(): array
    {
        return [
            'plate' => [
                'required',
                'string',
                'max:10',
                'unique:vehicles,plate'
            ],
            'brand' => [
                'required',
                'string',
                'max:50'
            ],
            'model' => [
                'required',
                'string',
                'max:50'
            ],
            'year' => [
                'required',
                'integer',
                'digits:4',
                'min:1980',
                'max:' . (date('Y') + 1)
            ],
            'type' => [
                'nullable',
                'string',
                'max:30'
            ],
            'patrimony_number' => [
                'nullable',
                'string',
                'max:50',
                'unique:vehicles,patrimony_number'
            ],
            'current_odometer' => [
                'nullable',
                'integer',
                'min:0'
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->plate) {
            $this->merge([
                'plate' => strtoupper($this->plate)
            ]);
        }
    }
}