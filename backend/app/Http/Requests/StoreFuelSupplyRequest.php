<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFuelSupplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\FuelSupply::class);
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                'exists:vehicles,id'
            ],
            'driver_id' => [
                'required',
                'exists:users,id,role,driver,active,1'
            ],
            'liters' => [
                'required',
                'numeric',
                'min:0.1',
                'max:1000'
            ],
            'price_per_liter' => [
                'required',
                'numeric',
                'min:0.01'
            ],
            'odometer' => [
                'required',
                'integer',
                'min:0'
            ],
            'supplied_at' => [
                'nullable',
                'date'
            ],
            'fuel_station' => [
                'nullable',
                'string',
                'max:100'
            ],
            'fuel_type' => [
                'nullable',
                'string',
                'max:50'
            ]
        ];
    }
}