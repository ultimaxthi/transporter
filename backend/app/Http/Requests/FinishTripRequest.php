<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinishTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('finish', $this->route('trip'));
    }

    public function rules(): array
    {
        $trip = $this->route('trip');

        return [
            'final_odometer' => [
                'required',
                'integer',
                'min:' . ($trip->initial_odometer ?? 0)
            ]
        ];
    }
}