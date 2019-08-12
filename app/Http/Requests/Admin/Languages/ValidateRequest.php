<?php

namespace App\Http\Requests\Admin\Languages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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
                    'flag_country' => ['required', 'string', 'size:2'],
                    'is_active' => ['nullable', 'boolean'],
                    'code' => ['required', 'string', 'size:2', 'unique:languages'],
                    'iso' => ['required', 'string', 'max:255'],
                    'file' => ['required', 'string', 'max:255'],
                    'name' => ['required', 'string', 'max:255']
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'flag_country' => ['required', 'string', 'size:2'],
                    'is_active' => ['nullable', 'boolean'],
                    'code' => Rule::unique('languages')->ignore($this->route()->language->id),
                    'iso' => ['required', 'string', 'max:255'],
                    'file' => ['required', 'string', 'max:255'],
                    'name' => ['required', 'string', 'max:255']
                ];
            }
            default: break;
        }
    }

    public function messages() :array
    {
        return [
            'code.unique' => Lang::get('validation.language_code_must_be_unique')
        ];
    }
}

