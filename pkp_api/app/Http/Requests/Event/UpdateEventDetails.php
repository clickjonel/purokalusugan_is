<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventDetails extends FormRequest
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
            'event_id' => 'required|numeric',
            'event_name' => 'required|string',
            'event_venue' => 'required|string',
            'event_budget' => 'required|numeric',
            'event_actual_budget' => 'required|numeric',
            'event_fund_source' => 'required|string',
            'event_proponents' => 'required|string',
            'event_partners' => 'required|string',
            //'programs' => 'required|array',
            'event_date_start' => 'required|date_format:Y-m-d',
            'event_date_end' => 'required|date_format:Y-m-d',
            //'barangays' => 'required|array',
            'event_type' => 'required|numeric|in:1,2', 
        ];
    }
}
