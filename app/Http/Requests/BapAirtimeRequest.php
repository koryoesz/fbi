<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BapAirtimeRequest extends FormRequest
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
            'service_type' => 'required|in:mtn,glo',
            'phone' => 'required|regex:/^(\+?\d{10,11})$/',
            'amount'    => 'required|numeric',
            'plan'  => 'required',
            'agent_id'  => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'service_type.in' => 'Invalid network provider.',
            'phone_number.regex'    => 'Invalid phone number.'
        ];
    }
}
