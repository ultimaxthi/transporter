<?php

namespace App\Http\Requests;

use App\Enums\MaintenanceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreVehicleMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\VehicleMaintenance::class);
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => [
                'required',
                'exists:vehicles,id'
            ],
            'type' => [
                'required',
                new Enum(MaintenanceType::class)
            ],
            'description' => [
                'required',
                'string',
                'max:500'
            ],
            'odometer' => [
                'nullable',
                'integer',
                'min:0'
            ],
            'cost' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'start_date' => [
                'required',
                'date'
            ]
        ];
    }
}