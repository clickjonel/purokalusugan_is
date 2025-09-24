<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class PopulateEventRequest extends FormRequest
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
            'event_id' => 'required|integer',
            'barangays' => 'required|array',
            'barangays.*.barangay_id' => 'required|integer',
            'barangays.*.programs' => 'required|array',
            'barangays.*.programs.*.program_id' => 'required|integer',
            'barangays.*.programs.*.indicators' => 'required|array',
            'barangays.*.programs.*.indicators.*.indicator_id' => 'required|integer',
            'barangays.*.programs.*.indicators.*.disaggregations' => 'required|array',
            'barangays.*.programs.*.indicators.*.disaggregations.*.disaggregation_id' => 'required|integer',
            'barangays.*.programs.*.indicators.*.disaggregations.*.indicator_disaggregation_id' => 'required|integer',
            'barangays.*.programs.*.indicators.*.disaggregations.*.value' => 'nullable|integer',
            'barangays.*.programs.*.indicators.*.disaggregations.*.remarks' => 'nullable|string',
        ];
    }
}
