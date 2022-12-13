<?php

namespace Credpal\Configurations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'configurations' => 'required|array',
            'configurations.*.name' => 'required|string',
            'configurations.*.title' => 'required|string',
            'configurations.*.default' => 'nullable',
            'configurations.*.value' => 'nullable',
            'configurations.*.value_type' => 'nullable',
            'configurations.*.data' => 'nullable|json',
        ];
    }
}
