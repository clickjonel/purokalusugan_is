<?php

namespace App\Http\Requests\HRH;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateHrhRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'user_code' => 'required|string',
            // 'image' => 'nullable|string',
            // 'username' => 'required|string',
            // 'password' => 'required|string',
            'prefix' => 'nullable|string',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'suffix' => 'nullable|string',
            'nickname' => 'required|string',
            'email_address' => 'required|string',
            'contact_no' => 'required|string',
            // 'account_status' => 'required|string|in:Assigned,Unassigned',
        ];
    }
}
