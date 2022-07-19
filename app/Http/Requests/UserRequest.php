<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone_number' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'birth_date'=>'required|date',
            'gender'=> ['required', Rule::in(['f','m'])],
            'national_id'=>'required|numeric',
            'campus_card_id'=>'required',
            'campus_unit_number' => 'required|numeric',
            'campus_card_expiry_date' => 'required|date',
            'study_specialty'=>'required',
            'study_year'=>'required',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
