<?php

namespace App\Http\Requests\Admin\Contents;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        switch ($this->method()) {
            case 'POST':
            {
                return [
//                    'title'=>['required', 'string']
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
//                    'title'=>['nullable', 'string']
                ];
            }
            default: break;
        }
    }
}
