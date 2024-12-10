<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShagoAirtimeRequest extends FormRequest
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
            'network' => 'required|in:MTN,GLO',
            'phone' => 'required|regex:/^(\+?\d{10,11})$/',
            'amount'    => 'required|numeric',
            'vend_type'  => 'required',
            'serviceCode'  => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'network.in' => 'Invalid network provider.',
            'phone_number.regex'    => 'Invalid phone number.'
        ];
    }
}
