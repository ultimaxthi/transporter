<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartTripRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'initial_odometer' => ['required','integer','min:0']
        ];
    }
}