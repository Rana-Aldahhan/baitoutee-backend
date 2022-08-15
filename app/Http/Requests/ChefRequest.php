<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ChefRequest extends FormRequest
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
            'phone_number' => 'required|unique:chef_join_requests,phone_number,'.request()->route('id'),
            'name' => 'required',
            'email' => 'required|email|unique:chef_join_requests,email,'.request()->route('id'),
            'birth_date'=>'required|date',
            'gender'=> ['required', Rule::in(['f','m'])],
            'delivery_starts_at' =>['required'],
            'delivery_ends_at' =>['required'],
            'max_meals_per_day'=>'required|numeric',
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
