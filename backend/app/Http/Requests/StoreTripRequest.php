<?php

namespace App\Http\Requests;

use App\Enums\TripPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Trip::class);
    }

    public function rules(): array
    {
        return [
            'operator_id' => [
                'required',
                'exists:users,id,role,operator'
            ],
            'driver_id' => [
                'nullable',
                'exists:users,id,role,driver,active,1'
            ],
            'vehicle_id' => [
                'nullable',
                'exists:vehicles,id'
            ],
            'patient_name' => [
                'required',
                'string',
                'max:100'
            ],
            'origin_street' => [
                'required',
                'string',
                'max:100'
            ],
            'origin_neighborhood' => [
                'required',
                'string',
                'max:100'
            ],
            'destination_street' => [
                'required',
                'string',
                'max:100'
            ],
            'destination_neighborhood' => [
                'required',
                'string',
                'max:100'
            ],
            'destination_city' => [
                'nullable',
                'string',
                'max:100'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500'
            ],
            'priority' => [
                'nullable',
                new Enum(TripPriority::class)
            ]
        ];
    }
}