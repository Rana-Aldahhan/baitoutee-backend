<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class MealRequest extends FormRequest
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
            'name' => 'required|min:1|max:50',
            'category_name' => 'required_without:category_id',
            'ingredients' => 'required',
            'expected_preparation_time' => 'required|numeric|max:255',
            'max_meals_per_day' => ['required', 'numeric', 'min:0', 'max:255'], // check if this make a problem
            'price' => 'required|numeric',
            'discount_percentage' => 'nullable|numeric',
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
