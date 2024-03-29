<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliverymanRequest extends FormRequest
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
            'phone_number' => 'required|unique:deliverymen,phone_number,'.request()->route('id'),
            'name' => 'required',
            'email' => 'required|email|unique:deliverymen,email,'.request()->route('id'),
            'birth_date'=>'required|date',
            'gender'=> ['required', Rule::in(['f','m'])],
            // 'transportation_type'=>['required', Rule::in([0,1,2,3])],
            'work_days'=>'required',
            'work_hours_from'=>'required',
            'work_hours_to'=>'required',
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
