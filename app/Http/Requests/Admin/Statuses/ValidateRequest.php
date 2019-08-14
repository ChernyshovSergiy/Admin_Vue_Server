<?php

namespace App\Http\Requests\Admin\Statuses;

use Illuminate\Foundation\Http\FormRequest;

class ValidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'en' => ['required', 'string', 'max:20']
        ];
    }
}
