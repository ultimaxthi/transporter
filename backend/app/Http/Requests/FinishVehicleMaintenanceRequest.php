<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinishVehicleMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('finish', $this->route('maintenance'));
    }

    public function rules(): array
    {
        return [
            'end_date' => [
                'required',
                'date',
                'after_or_equal:' . $this->route('maintenance')->start_date
            ],
            'cost' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'description' => [
                'nullable',
                'string',
                'max:500'
            ]
        ];
    }
}