<?php

return [
    'entities'=>[
        'user'=>'مستخدم',
        'users'=>'مستخدمين',
        'chef'=>'طاهي',
        'chefs'=>'طهاة',
        'deliveryman'=>'عامل توصيل',
        'deliverymen'=>'عمال التوصيل',
        'admin'=>'مشرف',
        'admins'=>'مشرفين',
        'order'=>'طلب',
        'orders'=>'طلبات',
        'meal'=>'وجبة',
        'meals'=>'وجبات',
        'price_change_request'=>'طلب تغيير السعر',
        'price_change_requests'=>'طلبات تغيير السعر',
        'user_join_request'=>'طلب انضمام المستخدم',
        'user_join_requests'=>'طلبات انضمام المستخدمين',
        'chef_join_request'=>'طلب انضمام طاهي',
        'chef_join_requests'=>'طلبات انضمام الطهاة',
        'deliveryman_join_request'=>'طلب انضمام عامل توصيل',
        'deliveryman_join_requests'=>'طلبات انضمام عمال التوصيل',
        'report'=>'إبلاغ',
        'reports'=>'إبلاغات',
        'category'=>'صنف',
        'categories'=>'أصناف',
        'delivery'=>'عملية توصيل',
        'deliveries'=>'عمليات التوصيل',
        'subscription'=>'اشتراك',
        'subscriptions'=>'اشتراكات',
        'the_admins'=>'المشرفين',

    ],
    'actions'=>[
        'block'=>'حجب',
        'unblock' => 'إلغاء الحجب',
        'accept'=>'قبول',
        'reject'=>'رفض',
        'accepted'=>'مقبول',
        'rejected'=>'مرفوض',
        'refresh'=>'تحديث',
        'edit_values'=>'تعديل القيم',
        'save'=>'حفظ',
        'filter'=>'تصفية النتائج',
        'mark_as_seen'=>'تحديد كمشاهد',
        'seen'=>'تمت مشاهدته',
        'unseen'=>'غير مشاهدة',
        'all_results'=>'كل النتائج',
    ],
    'titles'=>[
        'manage_profit_values'=>'إدارة قيم الأرباح',
        'show_users'=>'استعراض المستخدمين',
        'manage_join_requests'=>'إدارة طلبات الانضمام',
        'manage_financial_affairs'=>'إدارة الأمور المالية',
        'manage_orders'=>'إدارة الطلبات',
        'manage_pending_orders'=>' الطلبات المعلّقة'

    ],
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
        'max_meals_per_day' => 'عدد الوجبات الأعظمي التي يمكن تحضيرها باليوم',
        'profile_picture' => 'الصورة الشخصية',
        'expected_preparation_time' => 'الوقت المتوقع لتحضير الوجبة',
        'meals.max_meals_per_day' => 'العدد الأعظمي الممكن تحضيره من هذه الوجبة يوميا',
        'ingredients' => 'المكونات',
        'discount_percentage'=>'نسبة الخصم على السعر',
        'is_available' =>'متاح',
        'certificate'=>'الشهادة',
        'approved'=> 'مقبولة',
        'price' =>'السعر',
        'image' =>'الصورة',
        'balance'=>'الرصيد',
        'selected_delivery_time'=>'الوقت المحدد للتوصيل',
        'new_status'=>'الحالة الجديدة',
        'reported_on'=>'الجهة المبلغة عنها',
        'reason'=>'السبب',
        'current_longitude'=>'خط الطول الحالي',
        'current_latitude'=>'خط العرض الحالي',
        'longitude'=>'خط الطول ',
        'latitude'=>'خط العرض ',
        'approved_at'=>'تاريخ القبول',
        'deleted_at'=>'تاريخ الحذف',
        'created_at'=>'تاريخ الإنشاء',
        'transportation_type'=>'نوع وسيلة النقل',
        'work_days'=>'أيام العمل',
        'work_hours_from'=>'وقت بداية ساعات العمل',
        'work_hours_to'=>'وقت نهاياة ساعات العمل',
        'total_collected_order_costs'=>'المبلغ المقبوض',
        'role'=>'الدور',
        'mealProfit'=>'أرباح الوجبة الواحدة ',
        'kmCost'=>'تكلفة ال km الواحد',
        'deliveryPercentage'=>'نسبة عامل التوصيل من كلفة عملية التوصيل',
        'approved'=>'مقبول',
        'sendable'=>'المبلغ',
        'receivable'=>'المبلغ عنه',
        'sendable_type'=>'نوع المبلغ',
        'receivable_type'=>'نوع المبلغ عنه',
        'order'=>'طلب',
        'seen'=>'تمت مشاهدته',
        'rating'=>'التقييم',
        'rates_count'=>'عدد التقييمات',
        'updated_at'=>'تاريخ التعديل',
        'old_price'=>'السعر القديم',
        'new_price'=>'السعر الجديد',
        'notes'=>'الملاحظات',
        'status'=>'الحالة',
        'total_cost'=>'الكلفة الكلية',
        'meals_cost'=>'كلفة الوجبات',
        'profit'=>'الأرباح',
        'accepted_at'=>'تاريخ القبول',
        'prepared_at'=>'تاريخ التحضير',
        'paid_to_chef'=>'تم دفعه للطاهي',
        'paid_to_accountant'=>'تم دفعه للمحساب',
        'order_details'=>'تفاصيل الطلب'
    ],

];