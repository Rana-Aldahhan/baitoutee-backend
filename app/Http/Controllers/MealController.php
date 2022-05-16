<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MealController extends Controller
{
        private function getupdateRules()
        {
            return [
                'image' => 'filled|image',
                'name' => 'filled|min:1|max:50',
                'category_id'=>'filled|exists:categories,category_id',
                'expected_preparation_time' => 'filled|numeric|max:255',
                'max_meals_per_day' => ['filled','numeric','min:0','max:255','maxMeals'],  // check if this make a problem
                'price' => 'filled|numeric',
                'discount_percentage'=>'date',
            ];
        }
        protected function getRules(){
            return [
                'image' => 'required|image',
                'name' => 'required|min:1|max:50',
                'category_id'=>'filled|exists:categories,category_id',
                'ingredients' => 'required',
                'expected_preparation_time' => 'required|numeric|max:255',
                'max_meals_per_day' => ['required','numeric','min:0','max:255','maxMeals'],  // check if this make a problem
                'price' => 'required|numeric',
                'discount_percentage'=>'nullable|date',
                ];
    
        }

        protected function validateMeal(Request $request){
            $rules = $this->getRules();
            $messages = $this->getMessages();
            $validator = Validator::make($request->only('image', 'name',
                'category_id', 'ingredients', 'expected_preparation_time', 'max_meals_per_day',
                'price','discount_percentage'),
                $rules,$messages);
    
            if($validator->fails())//case of input validation failure
            {
                return $this->errorResponse($validator->errors()->first(),422);
            }
        }
        /*
         * Display a listing of the resource.
         *
         * @return JsonResponse
         */
        public function indexCategories(){
            $categories_id = auth('chef')->user()->meals->pluck('category_id')->unique();
            $categories = Category::whereIn('id', $categories_id->toArray())->get(['id','name']);
            return $this->successResponse($categories);
        }
        public function getMealOfCategory($id)
        {
            $meals = auth('chef')->user()->meals;
            /// $request->route('id'); or  $request->id; (try)
            $categoryMeals = $meals->where('category_id', $id);
            return $this->successResponse($categoryMeals);
        }
    
        /*
         * Store a newly created resource in storage.
         *
         * @param Request $request
         * @return JsonResponse
         */
    
        public function store(Request $request)
        {
            // validate it is a chef in middleware
            // validate the meal in StoreMealRequest
            // $validated = $request->validated();
    
            // it is better to use form request StoreMealRequest
            // it is not good to use $request->all()
    
            $this->validateMeal($request);
            $imagePath=null;
            if($request->hasFile('image')){
                // Get filename with the extension
                $filenameWithExt = $request->file('image')->getClientOriginalName();
                // Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('image')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore= $filename.'_'.time().'.'.$extension;
                // Upload Image
                $imagePath = $request->file('image')->storeAs('public/mealImages', $fileNameToStore);
                //$profilePath=asset('storage/profiles/'.$fileNameToStore);
            }
            $newMeal =Meal::create([
                'image'=>$imagePath,
                'name'=>$request['name'],
                'category_id'=>$request['category_id'],
                'ingredients'=>$request['ingredients'],
                'expected_preparation_time'=>$request['expected_preparation_time'],
                'max_meals_per_day'=>$request['max_meals_per_day'],
                'price'=>$request['price'],
                'discount_percentage'=>$request['discount_percentage'], // check if this make a problem if it was null
            ]);
            if($newMeal){
                return $this->successResponse($newMeal,201);
            }else{
                return $this->errorResponse("لم يتمكن من إنشاء وجبة",400);
            }
        }
    
        /*
         * Display the specified resource.
         *
         * @param  Meal  $meal
         * @return JsonResponse
         */
        public function show(Meal  $meal)
        {
            if($meal){
                return $this->successResponse($meal);
            }
            else{
                return $this->errorResponse("لم يتمكن من عرض الوجبة",404);
            }
        }
    
    
        /*
         * Update the specified resource in storage.
         *
         * @param Request $request
         * @param  Meal $meal
         * @return JsonResponse
         */
        public function update(Request $request,Meal $meal)
        {
            //create fillable property
            $this->validateMeal($request);
            $input = $request->all();
            $updatedMeal = $meal->fill($input)->save(); // check if it is working or do update
            if($updatedMeal){
                return $this->successResponse($updatedMeal);
            }else{
                return $this->errorResponse("لم يتمكن من تعديل معلومات الوجبة",404);
            }
        }
    
        /**
         * Remove the specified resource from storage.
         *
         * @param  Meal  $meal
         * @return JsonResponse
         */
        public function destroy(Meal $meal)
        {
            $success = $meal->delete();
            if($success){
                //$message = str("تم حذف الوجبة ".$meal->name)->after;
                return $this->successResponse($success,200);
            }else{
                return $this->errorResponse("لم يتمكن من حذف الوجبة",404);
            }
        }
    
        public function editMaximumMealNumber(Request $request,Meal $meal){
            $request->merge([
                'newNumMeal' => intval($meal->max_meals_per_day + $request['value'])
            ]);
            //request()->request->add(['index'=>'value']); // if 👆🏻 not working try this
            $validator = Validator::make($request->only('value','newNumMeal'),
                ['value' => ['required','numeric',
                    Rule::in([-1, +1])],
                 'newNumMeal' => 'maxMeals']
                ,[],['newNumMeal' => 'العدد الأعظمي الممكن تحضيره من هذه الوجبة يوميا']);
    
            if($validator->fails())//case of input validation failure
            {
                return $this->errorResponse($validator->errors()->first(),422);
            }
    
            $updatedMeal = $meal->update([
                'max_meals_per_day' => $request['value']
            ]);
            if($updatedMeal){
                return $this->successResponse($updatedMeal);
            }else{
                return $this->errorResponse("لم يتمكن من تعديل معلومات الوجبة",404);
            }
    
        }
        public function editAvailability(Meal $meal){
            $availability = !$meal->is_available;
            $updatedMeal = $meal->update([
                'is_available' => $availability
            ]);
            if($updatedMeal){
                return $this->successResponse($updatedMeal);
            }else{
                return $this->errorResponse("لم يتمكن من تعديل معلومات الوجبة",404);
            }
        }
    
}
