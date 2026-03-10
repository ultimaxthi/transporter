<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // depois podemos conectar com policies
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')->id;

        return [
            'plate' => [
                'required',
                'string',
                'max:10',
                Rule::unique('vehicles', 'plate')->ignore($vehicleId),
            ],

            'brand' => [
                'required',
                'string',
                'max:50',
            ],

            'model' => [
                'required',
                'string',
                'max:50',
            ],

            'year' => [
                'required',
                'integer',
                'digits:4',
                'min:1980',
                'max:' . (date('Y') + 1),
            ],

            'type' => [
                'nullable',
                'string',
                'max:30',
            ],

            'patrimony_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('vehicles', 'patrimony_number')->ignore($vehicleId),
            ],

            'current_odometer' => [
                'nullable',
                'integer',
                'min:' . $this->route('vehicle')->current_odometer,
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->plate) {
            $this->merge([
                'plate' => strtoupper($this->plate),
            ]);
        }
    }
}