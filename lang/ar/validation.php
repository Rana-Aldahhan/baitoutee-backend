<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ' :attribute يجب أن تكون مقبولة',
    'accepted_if' => ' :attributeيجب أن تكون مقبولة عندما :other تكون :value.',
    'active_url' => ' :attribute ليس رابطاً صحيحاً.',
    'after' => ' :attribute يجب أن يكون تاريخاً بعد :date.',
    'after_or_equal' => ' :attribute يجب أن يكون تاريخاً مساوٍ أو بعد  :date.',
    'alpha' => ' :attribute يجب أن يحتوي على أحرف فقط',
    'alpha_dash' => ' :attribute يجب أن يتتضمن حروف،أرقام،dashes، underscores حصراً.',
    'alpha_num' => ' :attribute  يجب أن يتتضمن حروف،أرقام.',
    'array' => ' :attribute يجب أن تكون مصفوفة.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => ' :attribute يجب أن يحمل قيمة بوليانية فقط.',
    'confirmed' => ' :attribute التأكيد ليس متطابق.',
    'current_password' => 'كلمة السر  غير صحيحة.',
    'date' => ':attribute ليس تاريخاً صالحاً.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => ':attribute يجب أن يطابق الصيغة :format.',
    'declined' => 'The :attribute must be declined.',
    'declined_if' => 'The :attribute must be declined when :other is :value.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => ' :attribute يجب أن يكون بريد الكتروني صالح.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute يجب أن يكون ملفاً.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => ' :attribute يجب أن تكون أكبر من  :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute يجب أن تكون أكبر من  :value.',
        'file' => 'The :attribute must be greater than or equal to :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal to :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => ':attribute يجب أن تكون صورة.',
    'in' => 'قيمة :attribute غير صالحة.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'file' => 'The :attribute must be less than or equal to :value kilobytes.',
        'string' => 'The :attribute must be less than or equal to :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max' => [
        'numeric' => ' :attribute يجب ألا تكون أكبر من  :max.',
        'file' => 'The :attribute must not be greater than :max kilobytes.',
        'string' => 'The :attribute must not be greater than :max characters.',
        'array' => 'The :attribute must not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => ':attribute يجب أن يكون رقماً.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => ' :attribute هو حقل مطلوب.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => ' :attribute يجب أن يكون :size.',
        'file' => ' :attribute يجب أن يخضع لحجم أقل :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid timezone.',
    'unique' => ' :attribute محجوز مسبقاً.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attributeيجب أن يكون رابطاً صالحاً .',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'max_meals_per_day' => [
            'MaximumMealNumber' => ':attribute يتجاوز عدد الحصص الكلية',
        ],'image-link' => [
            'Imagelink' => ':attribute ليس رابط صورة',
        ],
        'delivery_ends_at'=>[
            'TimeAfter'=>' :attribute يجب أن يكون أكبر من الوقت السابق'
        ],
        'selected_delivery_time'=>[
            'InChefDeliveryRange'=>' :attribute يجب أن يكون ضمن الأوقات المتاحة للتوصيل  '
        ],
        'selected_max_meals_per_day' =>[
            'MoreThanMaxMeals' => ' العدد الأعظمي يجب أن يكون أصغر من :attribute  '
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name'=>'الاسم',
        'password'=>'كلمة السر',
        'phone_number' => 'رقم الهاتف',
        'phone' => 'رقم الهاتف',
        'email' => 'البريد الالكتروني',
        'birth_date'=>'تاريخ الميلاد',
        'gender'=> 'الجنس',
        'national_id'=>'الرقم الوطني',
        'campus_card_id'=>'رقم بطاقة السكن',
        'campus_unit_number' => 'رقم وحدة السكن',
        'campus_card_expiry_date' => 'تاريخ انتهاء صلاحية بطاقة السكن',
        'study_specialty'=>'التخصص الدراسي',
        'study_year'=>'السنة الدراسية',
        'location'=>'الموقع',
        'code'=>'رمز التحقق',
        'delivery_starts_at'=>'وقت بداية التوصيل المتاحة',
        'delivery_ends_at' => 'وقت نهاية التوصيل المتاحة',
        'max_meals_per_day' => 'عدد الوجبات الكلي الممكن تحضيره',
        'profile_picture' => 'الصورة الشخصية',
        'expected_preparation_time' => 'الوقت المتوقع لتحضير الوجبة',
        'meals.max_meals_per_day' => 'العدد الأعظمي الممكن تحضيره من هذه الوجبة يوميا',
        'ingredients' => 'المكونات',
        'discount_percentage'=>'نسبة الخصم على السعر',
        'is_available' =>'متاحة',
        'approved'=> 'مقبولة',
        'price' =>'السعر',
        'image' =>'الصورة',
        'selected_delivery_time'=>'الوقت المحدد للتوصيل',
        'new_status'=>'الحالة الجديدة',
        'reported_on'=>'الجهة المبلغة عنها',
        'reason'=>'السبب',
        'current_longitude'=>'خط الطول الحالي',
        'current_latitude'=>'خط العرض الحالي',
        'meal_profit' => 'ربح الوجبة',
        'cost_of_one_km' => 'تكلفة ال km الواحد',
        'delivery_profit_percentage' => 'نسبة ربح عامل التوصيل من التوصيلة',

    ],

];
