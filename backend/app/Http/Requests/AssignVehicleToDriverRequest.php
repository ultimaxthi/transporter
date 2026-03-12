<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignVehicleToDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('assignDriver', $this->route('vehicle'));
    }

    public function rules(): array
    {
        return [
            'driver_id' => [
                'required',
                'exists:users,id,role,driver,active,1'
            ],
            'assigned_at' => [
                'nullable',
                'date'
            ]
        ];
    }
}