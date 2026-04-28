<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_name'             => ['required', 'string', 'max:255'],
            'priority'                 => ['required', 'in:normal,high,emergency'],
            'origin_street'            => ['required', 'string', 'max:255'],
            'origin_neighborhood'      => ['required', 'string', 'max:255'],
            'destination_street'       => ['required', 'string', 'max:255'],
            'destination_neighborhood' => ['required', 'string', 'max:255'],
            'destination_city'         => ['nullable', 'string', 'max:255'],
            'observations'             => ['nullable', 'string'],
            'driver_id'                => ['nullable', 'exists:users,id'],
            'vehicle_id'               => ['nullable', 'exists:vehicles,id'],
        ];
    }
}