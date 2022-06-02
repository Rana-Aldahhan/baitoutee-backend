<?php

namespace App\Http\Controllers;

use App\Models\PriceChangeRequest;
use App\Rules\ImageLink;
use App\Rules\MaximumMealNumber;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;
use phpDocumentor\Reflection\Types\Boolean;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\True_;


class MealController extends Controller
{

    /**
     * helper methode to get the rules to validate meal
     * @return array
     */
    protected function getRules(Request $request)
    {
        return [
            'image' => 'required|image',
            'name' => 'required|min:1|max:50',
            'category_id' => 'required_without:new_category_name|exists:categories,id',
            'category_name' => 'required_without:category_id',
            'ingredients' => 'required',
            'expected_preparation_time' => 'required|numeric|max:255',
            'max_meals_per_day' => ['required', 'numeric', 'min:0', 'max:255', new MaximumMealNumber($request['max_meals_per_day'])],  // check if this make a problem
            'price' => 'required|numeric',
          //  'reason' => new RequiredIf($this->changedPrice),
            'discount_percentage' => 'nullable|numeric',
        ];

    }
    protected function getUpdateRules(Request $request)
    {

        return [
            'image' => 'filled',
            'name' => 'filled|min:1|max:50',
            'category_id' => 'filled|exists:categories,id',
            'ingredients' => 'filled',
            'expected_preparation_time' => 'filled|numeric|max:255',
            'max_meals_per_day' => ['filled', 'numeric', 'min:0', 'max:255', new MaximumMealNumber($request['max_meals_per_day'])],  // check if this make a problem
            'price' => 'filled|numeric',
           // 'reason' => new RequiredIf($this->changedPrice),
            'discount_percentage' => 'nullable|numeric',
        ];

    }

    /**
     * helper method to validate a meal
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator|JsonResponse
     */
    protected function validateMeal(Request $request,$rules)
    {
        $validator = Validator::make($request->only('image', 'name',
            'category_id', 'ingredients', 'expected_preparation_time', 'max_meals_per_day',
            'price', 'discount_percentage', 'new_category_name'),
            $rules);
        if ($validator->fails())//case of input validation failure
        {
            return $this->errorResponse($validator->errors()->first(), 422);
        } else
            return $validator;
    }

    private function getCategories($categories_id){
        $categories = Category::whereIn('id', $categories_id)->get(['id', 'name']);
        $categories->push(Category::all('id', 'name')->first());
        $categories->push(Category::all('id', 'name')->skip(1)->first());
        return $categories->unique();
    }
    /**
     * Display a listing of categories
     *
     * @return JsonResponse
     */
    public function indexCategories()
    {
        $categories_id = auth('chef')->user()->meals->pluck('category_id')->unique();
        return $this->successResponse($this->getCategories($categories_id));
    }

    /**
     * get the chef meals of a specific category
     * @param $id
     * @return JsonResponse
     */
    public function getMealOfCategory($id)
    {
        $meals = auth('chef')->user()->meals;
        /// $request->route('id'); or  $request->id; (try)
        /// $category->meals (try this instead 👈🏻)
        $categoryMeals = $meals->where('category_id', $id);
        $categoryMeals->toArray();
       /* foreach ($categoryMeals as $categoryMeal){
            $categoryMeal->image =  asset($categoryMeal->image);
        }*/

        return $this->successResponse($categoryMeals->filter());
    }

    /**
     * get the number of active meals and the number of total meals
     * of the chef meals
     * @return JsonResponse
     */
    public function getActiveMealsCount()
    {
        $ChefMeals = auth('chef')->user()->meals;
        $countActive = $ChefMeals->where('is_available', true)->count();
        $countNonActive = $ChefMeals->count() - $countActive;
        $mealsCount = Collection::make([
            'active_meals' => $countActive,
            'total_meals' => $countNonActive,
        ]);
        return $this->successResponse($mealsCount);
    }

    /**
     * show the chef the price that will be shown to the students
     * by adding the application profit
     * @param  $price
     * @return JsonResponse
     */
    public function getPriceForStudent($price)
    {
       /* $validator = Validator::make([$price], ['price' => 'required|numeric']);
        if ($validator->fails())//case of input validation failure
            return $this->errorResponse($validator->errors()->first(), 422);*/
        $meal_profit =DB::table('global_variables')->where('id',3)->get('value')->first();
        $student_price = intval($price) + intval($meal_profit->value);
        $price = Collection::make([
            'student_price' => $student_price
        ]);
        return $this->successResponse($price);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $rules = $this->getRules($request);
        $validateResponse = $this->validateMeal($request,$rules);

        if ($validateResponse instanceof JsonResponse) {
            return $validateResponse;
        }

        $imagePath = $this->storeMealPic($request);
        //TODO : enhance create by using validateResponse
        $newMeal = Meal::create([
            'chef_id' =>  auth('chef')->id(),
            'image' => $imagePath,
            'name' => $request['name'],
            'ingredients' => $request['ingredients'],
            'expected_preparation_time' => $request['expected_preparation_time'],
            'max_meals_per_day' => $request['max_meals_per_day'],
            'price' => $request['price'],
            'discount_percentage' => $request['discount_percentage'], // check if this make a problem if it was null
            'category_id' =>  $request['category_id']
        ]);

        if ($newMeal->exists) {
          //  $newMeal->image = asset($newMeal->image);
            return $this->successResponse($newMeal, 201);
        } else {
            return $this->errorResponse("لم يتمكن من إنشاء وجبة", 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Meal  $meal
     * @return JsonResponse
     */
    public function show(Meal $meal)
    {
        if ($meal->exists) {
            return $this->successResponse($meal);
        } else {
            return $this->errorResponse("لم يتمكن من عرض الوجبة", 404);
        }
    }

    /**
     * helper function to store price change request entity
     *
     * @param Request $request
     * @param Meal $meal
     * @return void
     */
    private function storePriceChangeRequest(Request $request, Meal $meal)
    {
        PriceChangeRequest::create([
            'meal_id' => $meal->id,
            'new_price' => $request['price'],
            'reason' => $request['reason']
        ]);
    }

    //TODO: make it a trait so we don't repeat the code
    /**
     * helper method to store meal image
     * @param Request $request
     * @return false|string|null
     */
    private function storeMealPic(Request $request){
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Get filename with the extension
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('image')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            // Upload Image
            $imagePath = $request->file('image')->storeAs('public/mealImages', $fileNameToStore);
            //$profilePath=asset('storage/profiles/'.$fileNameToStore);
            $imagePath = '/storage/mealImages/' . $fileNameToStore;
        }
        return $imagePath;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  Meal $meal
     * @return JsonResponse
     */
    public function update(Request $request, Meal $meal)
    {
        // if the image had been updated then store the file
       // dd($request->all());
        $imagePath = $this->storeMealPic($request);

        $rules = $this->getUpdateRules($request);
        $oldMeal = Meal::find($meal->id);
        if($imagePath !=null){
            unlink(storage_path('app/public'.Str::after($oldMeal->image,'/storage')));
        }
        $validateResponse = $this->validateMeal($request,$rules);
        if ($validateResponse instanceof JsonResponse) {
            return $validateResponse;
        }

        if($request['price'] != null){
            if ($oldMeal->price != intval($request['price'])) {
                if($request['reason']== null){
                    return  $this->errorResponse("عند تغيير السعر حقل السبب مطلوب",400);
                }
                else{
                    $this->storePriceChangeRequest($request, $meal);
                }
            }
        }

        $updatedMeal = $meal->fill($validateResponse->validated())->save(); // check if it is working or do update

        if( $imagePath !=null){
            $meal->image = $imagePath;
            $meal->save();
        }
        if ($updatedMeal) {
            return $this->successResponse($updatedMeal);
        } else {
            return $this->errorResponse("لم يتمكن من تعديل معلومات الوجبة", 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Meal $meal
     * @return JsonResponse
     */
    public function destroy(Meal $meal)
    {
        $oldMeal = Meal::find($meal->id);
        $oldImage = storage_path('app/'.$oldMeal->image);
        if(File::exists($oldImage)){
                File::delete($oldImage);
        }
        $success = $meal->delete();
        if ($success) {
            //$message = str("تم حذف الوجبة ".$meal->name)->after;
            return $this->successResponse([], 200);
        } else {
            return $this->errorResponse("لم يتمكن من حذف الوجبة", 404);
        }
    }

    /**
     * subtracting one portion of a meal
     *
     * @param Meal $meal
     * @return JsonResponse
     */
    public function addMealNumber(Meal $meal)
    {
        $max_meals_per_day = auth('chef')->user()->get('max_meals_per_day')->first();
        $newNumMeal = $meal->max_meals_per_day + 1;
        if($newNumMeal>$max_meals_per_day)
            return $this->errorResponse("لقد تجاوزت العدد الأقصى من الوجبات",400);
        if($this->editMaximumMealNumber($meal,$newNumMeal)  == true) {
            return $this->successResponse($newNumMeal);
        }
        else{
            return $this->editMaximumMealNumber($meal,$newNumMeal);
        }

    }

    /**
     * adding one portion of a meal
     *
     * @param Meal $meal
     * @return JsonResponse
     */
    public function subtractMealNumber(Meal $meal)
    {
        $newNumMeal = $meal->max_meals_per_day - 1;
        if($newNumMeal<0)
            return $this->errorResponse("ادخال غير صالح",400);
        if($this->editMaximumMealNumber($meal,$newNumMeal)  == true) {
            return $this->successResponse($newNumMeal);
        }
        else{
            return $this->editMaximumMealNumber($meal,$newNumMeal);
        }

    }

    /**
     * helper methode to edit the number of portion of a meal
     *
     * @param Meal $meal
     * @param $newNumMeal
     * @return bool|JsonResponse
     */
    private function editMaximumMealNumber(Meal $meal,$newNumMeal)
    {
        $validator = Validator::make([$newNumMeal],
            ['newNumMeal' => 'maxMeals']
            , [], ['newNumMeal' => 'العدد الأعظمي الممكن تحضيره من هذه الوجبة يوميا']);

        if ($validator->fails())//case of input validation failure
            return $this->errorResponse($validator->errors()->first(), 422);

        $updatedMeal = $meal->update([
            'max_meals_per_day' => $newNumMeal
        ]);
        if ($updatedMeal) {
           return true;
        } else {
            return $this->errorResponse("لم يتمكن من تعديل معلومات الوجبة", 404);
        }

    }

    /**
     * edit the availability of a meal
     *
     * @param Meal $meal
     * @return JsonResponse
     */
    public function editAvailability(Meal $meal)
    {
        $availability = !$meal->is_available;
        $updatedMeal = $meal->update([
            'is_available' => $availability
        ]);
        if ($updatedMeal) {
            return $this->successResponse($updatedMeal);
        } else {
            return $this->errorResponse("لم يتمكن من تعديل معلومات الوجبة", 404);
        }
    }

    /**
     * store new category.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeCategory(Request $request)
    {
        if ($request['category_name'] != null) {
            $categories_id = auth('chef')->user()->meals->pluck('category_id')->unique();
            if ($this->getCategories($categories_id)->contains((function ($value, $key) {
                global $request;
                return ($value->name === $request['category_name']) ;
            }))) {
                return $this->errorResponse("اسم التصنيف الذي قمت بإدخاله موجود بالفعل", 400);
            }
            $newCategory = Category::create([
                'name' => $request['category_name']
            ]);
            return $this->successResponse($newCategory);
        }
        else{
            return  $this->errorResponse("حقل التصنيف مطلوب",422);
        }
    }

    public function getTopTenRated(){
        $topRatedMeals=Meal::approved()->sortBy(function($meal){
            //TODO follow the same sorting method of the top rated chefs
            return ($meal->rating + $meal->rates_count )/(5+$meal->rates_count);
        })->take(10)->get();
        return $this->successResponse($topRatedMeals,200);
    }
    public function getMealTopTenOffers(){
        $offers=Meal::approved()->where('discount_percentage','>',0)->sortBy(function($meal){
            return $meal->discount_percentage;
        })->take(10)->get();
        return $this->successResponse($offers,200);
    }
    public function getAllOffers(){
        $offersPagination=Meal::approved()->where('discount_percentage','>',0)->sortBy(function($meal){
            return $meal->discount_percentage;
        })->paginate(15);
        return $this->paginatedResponse($offersPagination);
    }
    public function getTopTenRecent(){
        $recentMeals=Meal::approved()->sortByDesc(function($meal){
            return $meal->created_at;
        })->take(10)->get();
        return $this->successResponse($recentMeals,200);
    }
    public function getTopTenOrdered(){
        //TODO check this
        $meals=Meal::approved()->withCount('orders')->get()->sortBy('orders_count')->take(10)->get();
        return $this->successResponse($meals);
    }

}

/** old functions **/
// create category route inside store meal and update meal
/* private function createCategory(Request $request, Meal $meal)
 {
     // the categoryname is unique
     if($request['category_id']!=null){
         $meal->category_id = $request['category_id'];
     }else if($request['new_category_name']!=null){
         // check if the name is unique
         $newCategory = Category::create([
             'name' =>$request['new_category_name']
         ]);
         $newCategoryId = $newCategory->id;
         $meal->category_id =$newCategoryId;
     }
     $meal->save();
 }

public function editMaximumMealNumber(Request $request, Meal $meal)
    {
        $request->merge([
            'newNumMeal' => intval($meal->max_meals_per_day + $request['value'])
        ]);
        //request()->request->add(['index'=>'value']); // if 👆🏻 not working try this
        $validator = Validator::make($request->only('value', 'newNumMeal'),
            ['value' => ['required', 'numeric',
                Rule::in([-1, +1])],
                'newNumMeal' => 'maxMeals']
            , [], ['newNumMeal' => 'العدد الأعظمي الممكن تحضيره من هذه الوجبة يوميا']);

        if ($validator->fails())//case of input validation failure
        {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $updatedMeal = $meal->update([
            'max_meals_per_day' => $request['value']
        ]);
        if ($updatedMeal) {
            return $this->successResponse($updatedMeal);
        } else {
            return $this->errorResponse("لم يتمكن من تعديل معلومات الوجبة", 404);
        }

    }
*/
