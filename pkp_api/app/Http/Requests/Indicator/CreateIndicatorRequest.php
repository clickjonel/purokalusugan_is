<?php

namespace App\Http\Requests\Indicator;

use Illuminate\Foundation\Http\FormRequest;

class CreateIndicatorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'program_id' => 'required|integer|exists:pkp_program,program_id',
            'indicator_name' => 'string',
            'indicator_description' => 'string',
            'indicator_scope' => 'integer' 
            ];
    }
}
