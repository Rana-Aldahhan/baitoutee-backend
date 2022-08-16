<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Chef;
use App\Models\Meal;
use App\Models\PriceChangeRequest;
use App\Models\User;
use App\Rules\MaximumMealNumber;
use App\Traits\MealsHelper;
use App\Traits\PictureHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\FCMService;

class MealController extends Controller
{
    use MealsHelper,PictureHelper;

    /**
     * helper methode to get the rules to validate meal
     * @return array
     */
    protected function getRules(Request $request)
    {
        return [
            'image' => 'required'/*|image'*/,
            'name' => 'required|min:1|max:50',
            'category_id' => 'required_without:new_category_name|exists:categories,id',
            'category_name' => 'required_without:category_id',
            'ingredients' => 'required',
            'expected_preparation_time' => 'required|numeric|max:255',
            'max_meals_per_day' => ['required', 'numeric', 'min:0', new MaximumMealNumber()], // check if this make a problem
            'price' => 'required|numeric|min:1',
            //  'reason' => new RequiredIf($this->changedPrice),
            'discount_percentage' => 'nullable|numeric|min:1|max:100',
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
            'max_meals_per_day' => ['filled', 'numeric', 'min:0', new MaximumMealNumber()], // check if this make a problem
            'price' => 'filled|numeric|min:1',
            // 'reason' => new RequiredIf($this->changedPrice),
            'discount_percentage' => 'nullable|numeric|min:1|max:100',
        ];

    }

    /**
     * helper method to validate a meal
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator|JsonResponse
     */
    protected function validateMeal(Request $request, $rules)
    {
        $validator = Validator::make($request->only('image', 'name',
            'category_id', 'ingredients', 'expected_preparation_time', 'max_meals_per_day',
            'price', 'discount_percentage', 'new_category_name'),
            $rules);
        if ($validator->fails()) { //case of input validation failure
            return $this->errorResponse($validator->errors()->first(), 422);
        } else {
            return $validator;
        }

    }

    public function getCategories($categories_id)
    {
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
        /// $request->route('id'); or  $request->id; (try)
        /// $category->meals (try this instead 👈🏻)
        $categoryMeals = auth('chef')->user()->meals()
        ->where('category_id', $id)
        ->where('approved',true)
        ->orWhereNull('approved')
        ->where('category_id', $id)
        ->where('chef_id', auth('chef')->user()->id)
        ->get()->map(function ($meal){
            return $meal->setHidden(['chef', 'category']);
        })->values();
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
        $totalCount = $ChefMeals->where('approved',true)->count();
        $mealsCount = Collection::make([
            'active_meals' => $countActive,
            'total_meals' => $totalCount,
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
        $meal_profit = DB::table('global_variables')->where('id', 3)->get('value')->first();
        $student_price = intval($price) + intval($meal_profit->value);
        $price = Collection::make([
            'student_price' => $student_price,
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
        $validateResponse = $this->validateMeal($request, $rules);

        if ($validateResponse instanceof JsonResponse) {
            return $validateResponse;
        }

        //$imagePath = $this->storeMealPic($request);
        $imagePath = $this->storePicture($request,'image','mealsImages');
        //TODO : enhance create by using validateResponse
        $newMeal = Meal::create([
            'chef_id' => auth('chef')->id(),
            'image' => $imagePath,
            'name' => $request['name'],
            'ingredients' => $request['ingredients'],
            'expected_preparation_time' => $request['expected_preparation_time'],
            'max_meals_per_day' => $request['max_meals_per_day'],
            'price' => $request['price'],
            'discount_percentage' => $request['discount_percentage'], // check if this make a problem if it was null
            'category_id' => $request['category_id'],
            'rates_count' =>0
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
        if ($meal->exists && $meal->approved) {
            $meal->is_saved = auth('user')->user()->savedMeals->where('id', $meal->id)->count() > 0;
            if($meal->discount_percentage!=null)
             $meal->price_after_discount=( $meal->price -( ( $meal->price * $meal->discount_percentage) /100)) +$this->getMealProfit();
            $meal->delivery_fee = $this->getMealDeliveryFee($meal->chef_id);
            $meal->price = $meal->price + $this->getMealProfit();
            $meal->remaining_available_meal_count = $meal->max_meals_per_day - $this->getCountOfTodayAssingedMeals($meal->chef, $meal);
            $meal->chef->remaining_available_chef_meals_count = $meal->chef()->get()->first()->max_meals_per_day - $this->getCountOfTodayAssingedTotalMeals($meal->chef);
            $meal->chef->location = $meal->chef()->get()->first()->location()->get()->first()->name;
            $meal->chef->delivery_starts_at = $meal->chef()->get()->first()->delivery_starts_at;
            $meal->chef->delivery_ends_at = $meal->chef()->get()->first()->delivery_ends_at;
            $meal->image = $meal->image;
            return $this->successResponse($meal);
        } else {
            return $this->errorResponse("لم يتمكن من عرض الوجبة", 404);
        }
    }

/**
 * the student will add the meal for the favorite meals
 * @param Meal $meal
 * @return void
 */
    public function addToFavorite(Meal $meal)
    {
        // add the user id and the meal id to the favorite table
        $data = auth('user')->user()->savedMeals()->firstOrCreate(['meals.id' => $meal->id]);
        if ($data->id != $meal->id) {
            return $this->errorResponse("الوجبة موجودة في قائمة المفضلة ", 400);
        }
        if ($data == null) {
            return $this->errorResponse("لم يتمكن من إضافة الوجبة إلى المفضلة ", 404);
        }
        return $this->successResponse($data, 200);

    }

/**
 * an end point to delete from favorite
 * @param Meal $meal
 * @return void
 */
    public function deleteFromFavorite(Meal $meal)
    {
        // add the user id and the meal id to the favorite table
        $data = auth('user')->user()->savedMeals()->find($meal->id);
        if ($data == null) {
            return $this->errorResponse("الوجبة غير موجودة في قائمة المفضلة", 404);
        } else {
            $data = auth('user')->user()->savedMeals()->detach($meal->id);
            return $this->successResponse($data, 200);
        }
    }

/**
 * helper function to store price change request entity
 *
 * @param Request $request
 * @param Meal $meal
 * @return void
 */
    public function storePriceChangeRequest(Request $request, Meal $meal)
    {
        PriceChangeRequest::create([
            'meal_id' => $meal->id,
            'new_price' => $request['price'],
            'reason' => $request['reason'],
        ]);
    }

//TODO: make it a trait so we don't repeat the code
/**
 * helper method to store meal image
 * @param Request $request
 * @return false|string|null
 */
    public function storeMealPic(Request $request)
    {
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
            $imagePath = $request->file('image')->storeAs('public/mealsImages', $fileNameToStore);
            //$profilePath=asset('storage/profiles/'.$fileNameToStore);
            $imagePath = '/storage/mealsImages/' . $fileNameToStore;
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
       // $imagePath = $this->storeMealPic($request);
       $imagePath = $this->storePicture($request,'image','mealsImages');

        $rules = $this->getUpdateRules($request);
        $oldMeal = Meal::find($meal->id);
        if ($imagePath != null) {
            unlink(storage_path('app/public' . Str::after($oldMeal->image, '/storage')));
        }
        $validateResponse = $this->validateMeal($request, $rules);
        if ($validateResponse instanceof JsonResponse) {
            return $validateResponse;
        }

        if ($request['price'] != null) {
            if ($oldMeal->price != intval($request['price'])) {
                if ($request['reason'] == null) {
                    return $this->errorResponse("عند تغيير السعر حقل السبب مطلوب", 400);
                } else {
                    $this->storePriceChangeRequest($request, $meal);
                }
            }
        }
        //if discount added send notification to all users
        if($request->discount_percentage> $oldMeal->discount_percentage)
        {
            FCMService::sendPushNotification(
                '/topics/user',
                'خصم جديد',
                "تم إضافة خصم جديد على وجبة ".$request->name." بنسبة ".$request->discount_percentage."%"
            );
        }
        $updatedMeal = $meal->fill($validateResponse->validated())->save(); // check if it is working or do update

        if ($imagePath != null) {
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
        unlink(storage_path('app/public' . Str::after($oldMeal->image, '/storage')));
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
        $max_meals_per_day = auth('chef')->user()->max_meals_per_day;
        $newNumMeal = $meal->max_meals_per_day + 1;
        if ($newNumMeal > $max_meals_per_day) {
            return $this->errorResponse("لقد تجاوزت العدد الأقصى من الوجبات", 400);
        }

        if ($this->editMaximumMealNumber($meal, $newNumMeal) == true) {
            return $this->successResponse($newNumMeal);
        } else {
            return $this->editMaximumMealNumber($meal, $newNumMeal);
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
        if ($newNumMeal < 0) {
            return $this->errorResponse("ادخال غير صالح", 400);
        }

        if ($this->editMaximumMealNumber($meal, $newNumMeal) == true) {
            return $this->successResponse($newNumMeal);
        } else {
            return $this->editMaximumMealNumber($meal, $newNumMeal);
        }

    }

/**
 * helper methode to edit the number of portion of a meal
 *
 * @param Meal $meal
 * @param $newNumMeal
 * @return bool|JsonResponse
 */
    public function editMaximumMealNumber(Meal $meal, $newNumMeal)
    {
        $validator = Validator::make([$newNumMeal],
            ['newNumMeal' => 'maxMeals']
            , [], ['newNumMeal' => 'العدد الأعظمي الممكن تحضيره من هذه الوجبة يوميا']);

        if ($validator->fails()) { //case of input validation failure
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $updatedMeal = $meal->update([
            'max_meals_per_day' => $newNumMeal,
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
    public function editAvailability(Meal $meal,Request $request)
    {
        $newAvailability = !$meal->is_available;
        if($newAvailability == true) {
            $portionNum = $meal->max_meals_per_day;
             auth('chef')->user()->meals()->where('is_available',1)->get()->map(function($meal)use (&$portionNum){
                $portionNum += $meal->max_meals_per_day;
            });
            $request->merge(['portionNum' =>$portionNum]);
            $msg = ' بتفعيل هذه الوجبة أصبح عدد الوجبات المتاحة للطلب هو ' . $portionNum . ' وهو أكبر من عدد الوجبات الممكن طلبها في اليوم ';
            $validator = Validator::make($request->all(),
            ['portionNum' => new MaximumMealNumber()]);
            if ($validator->fails()) { //case of input validation failure
                return $this->errorResponse($msg, 422);
            }
        }
        $updatedMeal = $meal->update([
            'is_available' => $newAvailability,
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
                return ($value->name === $request['category_name']);
            }))) {
                return $this->errorResponse("اسم التصنيف الذي قمت بإدخاله موجود بالفعل", 400);
            }
            $newCategory = Category::create([
                'name' => $request['category_name'],
            ]);
            return $this->successResponse($newCategory);
        } else {
            return $this->errorResponse("حقل التصنيف مطلوب", 422);
        }
    }
//TODO do we show only the active meals?
    public function getTopTenRated()
    {
        // following a method inspired by Bayesian probability
        // R refer to the "initial belief", 60th percentile (optimistic) or 40th percentile (pessimistic).
        // W is a fraction of number of rating, perhaps between C/20 and C/5 (depending on how noisy ratings are).
        // W = 0 is equivalent to using only the average of user ratings
        // W = infinity is equivalent to proclaiming that every item has a true rating of R
        // resource:
        // https://stackoverflow.com/questions/2495509/how-to-balance-number-of-ratings-versus-the-ratings-themselves
        $R = 2.5; // 50% of 5 => 2.5
        $W = 10.0; // assuming 20000 numbers of rates for an item /2000
        // (R*W + sigma(n*rating)/(W+sigma(n))
        // depending on R and W if there is no rating => the item rate will be 25/10 = 2.5
        $topRatedMeals = Meal::approved()
            ->where('rating', '!=', null) // delete this if we want to have defaults
            ->get()
            ->sorTByDesc(function ($meal) use ($R, $W) {
                return (($R * $W) + ($meal->rates_count * $meal->rating)) / ($W + $meal->rates_count);
            })
            ->take(10)
            ->values()
        ;
        //calculate the new price
        $topRatedMeals->map(function ($meal) {
            if($meal->discount_percentage !=null)
                $meal->price_after_discount=( $meal->price -( ( $meal->price * $meal->discount_percentage) /100)) +$this->getMealProfit();
            $meal->setHidden(['created_at', 'updated_at', 'approved', 'max_meals_per_day', 'expected_preparation_time', 'ingredients', 'category', 'category_id']);
            return $meal->price = $meal->price + $this->getMealProfit();
        });

        return $this->successResponse($topRatedMeals, 200);
    }
    //FIXME: may give the meals that are nor approved
    public function getMealTopTenOffers()
    {
        $offers = Meal::approved()
            ->where('discount_percentage', '>', 0)
            ->orWhere('category_id', 1) //category of offers
            ->orderByDesc('discount_percentage')
            ->take(10)
            ->get();
        //calculate the new price
        $offers->map(function ($meal) {
            if($meal->discount_percentage !=null)
             $meal->price_after_discount=( $meal->price -( ( $meal->price * $meal->discount_percentage) /100)) +$this->getMealProfit();
            $meal->setHidden(['created_at', 'updated_at', 'approved', 'max_meals_per_day', 'expected_preparation_time', 'ingredients', 'category', 'category_id']);
            return $meal->price = $meal->price + $this->getMealProfit();
        });
        return $this->successResponse($offers, 200);
    }
    //FIXME: may give the meals that are not approved
    public function getAllOffers()
    {
        $offersPagination = Meal::approved()
            ->where('discount_percentage', '>', 0)
            ->orWhere('category_id', 1) //category of offers
            ->orderByDesc('discount_percentage')
            ->paginate(15);
        $offersPagination->map(function ($meal) {
            if($meal->discount_percentage !=null)
             $meal->price_after_discount=( $meal->price -( ( $meal->price * $meal->discount_percentage) /100)) +$this->getMealProfit();
            $meal->setHidden(['created_at', 'updated_at', 'approved', 'max_meals_per_day', 'expected_preparation_time', 'ingredients', 'category', 'category_id']);
            return $meal->price = $meal->price + $this->getMealProfit() ;
        });
        return $this->paginatedResponse($offersPagination);
    }
    public function getTopTenRecent()
    {
        $recentMeals = Meal::approved()
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
        $recentMeals->map(function ($meal) {
            if($meal->discount_percentage !=null)
                $meal->price_after_discount=( $meal->price -( ( $meal->price * $meal->discount_percentage) /100)) +$this->getMealProfit();
            $meal->setHidden(['created_at', 'updated_at', 'approved', 'max_meals_per_day', 'expected_preparation_time', 'ingredients', 'category', 'category_id']);
            return $meal->price = $meal->price + $this->getMealProfit();
        });
        return $this->successResponse($recentMeals, 200);
    }
    public function getTopTenOrdered()
    {
        $meals = Meal::approved()
            ->withCount('orders')
            ->get()
            ->sortByDesc('orders_count')
            ->take(10)
            ->values();
        $meals->map(function ($meal) {
            if($meal->discount_percentage !=null)
                $meal->price_after_discount=( $meal->price -( ( $meal->price * $meal->discount_percentage) /100)) +$this->getMealProfit();
            $meal->setHidden(['created_at', 'updated_at', 'approved', 'max_meals_per_day', 'expected_preparation_time', 'ingredients', 'category', 'category_id']);
            return $meal->price = $meal->price + $this->getMealProfit() ;
        });
        return $this->successResponse($meals);
    }

    /**
    * index the meals for the chef
    * to choose when adding a new subscription
    */
    public function indexForChef()
    {
        $meals = auth('chef')->user()->meals()->where('approved', true)->get();
        $meals->map(function ($meal) {
            $meal->setHidden(['chef','approved','created_at','updated_at','chef_id','category_id']);
        });
        return $this->successResponse($meals);
    }

    // search for a meal function
    public function searchAndSort(Request $request)
    {
        // get the word want to search for and what to filter on
        $search = $request->search;
        $priceSortDesc = ($request->price_sort == 'desc') ? true : false;
        $rateSortDesc =  ($request->rate_sort == 'desc') ? true : false;

        //return the records that fit with the search
        $searched_meals = Meal::search($search)->query(function (Builder $builder)  {
            $builder->approved();
        })->paginate(10);

        // not the best but it is not bad if the meal have the exact name in the search
        //it will appear first
        $sortedMeals = $searched_meals->sortBy(function ($meal, $key) use ($search) {
            $meal->setHidden(['updated_at','created_at','chef_id','category_id','approved'
        ,'discount_percentage','price','max_meals_per_day','expected_preparation_time',
        'ingredients','category']);
            $mealPrice = $meal->price;
            if($meal->discount_percentage !=0){
                $mealWithDiscount = $mealPrice -(($mealPrice*$meal->discount_percentage)/100);
                $meal->price_with_discount = $mealWithDiscount +$this->getMealProfit();
            }
            else $meal->price_with_discount = null;
            $meal->price_without_discount = $mealPrice + $this->getMealProfit();
            $restNameLength = strlen($meal->name) - strlen($search);
            $restIngredientsLength = strlen($meal->ingredients) - strlen($search);
            return min($restNameLength,$restIngredientsLength);
        })->values();
        $sortedMeals =(($request->rate_sort!=null)? $sortedMeals->sortBy('rating',SORT_REGULAR,$rateSortDesc)->values():$sortedMeals);
       // $sortedMeals =(($request->price_sort!=null)? $sortedMeals->sortBy('price',SORT_REGULAR,$priceSortDesc)->values():$sortedMeals);
       $sortedMeals =(($request->price_sort!=null)? $sortedMeals->sortBy(function($meal) use ($request){
            if($meal->price_with_discount !=null){
                return $meal->price_with_discount;
            }else return $meal->price_without_discount;
        },SORT_REGULAR,$priceSortDesc)->values():$sortedMeals);
        $paginated_meals = new LengthAwarePaginator($sortedMeals, $searched_meals->total(), $searched_meals->perPage());
        return $this->paginatedResponse($paginated_meals);
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
