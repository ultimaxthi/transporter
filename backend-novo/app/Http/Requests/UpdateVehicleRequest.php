<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')?->id;

        return [
            'plate' => [
                'sometimes',
                'string',
                'max:10',
                "unique:vehicles,plate,{$vehicleId}"
            ],
            'brand'            => ['sometimes', 'string', 'max:50'],
            'model'            => ['sometimes', 'string', 'max:50'],
            'year'             => ['sometimes', 'integer', 'digits:4', 'min:1980', 'max:' . (date('Y') + 1)],
            'type'             => ['nullable', 'string', 'max:30'],
            'patrimony_number' => ['nullable', 'string', 'max:50', "unique:vehicles,patrimony_number,{$vehicleId}"],
            'current_odometer' => ['nullable', 'integer', 'min:0'],
            'status'           => ['sometimes', 'in:available,in_trip,in_maintenance,inactive'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->plate) {
            $this->merge(['plate' => strtoupper($this->plate)]);
        }
    }
}