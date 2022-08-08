<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Chef;
use App\Models\Meal;
use App\Models\Location;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;


class OrderTest extends TestCase
{
    use DatabaseTransactions,WithFaker;

    /**
     * make a successful order:
     */
    public function test_make_successful_order(){
        $knownDate = Carbon::create(2022, 8, 9, 12);
        Carbon::setTestNow($knownDate); 

        $faker = \Faker\Factory::create();
        $category=Category::factory()->create();
        $location=Location::factory()->create();
        $user = User::factory()->create([
            'location_id' => 1,
        ]);
        $chef=Chef::factory()->create([
            'is_available'=>true,
            'location_id'=>$location->id,
            'delivery_starts_at'=>now()->subHours(2),
            'delivery_ends_at'=>now()->addHours(2),
        ]);
        $meal=Meal::factory()->create([
            'chef_id'=>$chef->id,
            'category_id'=>$category->id,
            'is_available'=>true
        ]);
        $response = $this->actingAs($user,'user')
                         ->postJson('/api/user/make-order',
         [
            'chef_id' => $chef->id,
            'meals_count'=>1,
            'meals'=>[
                ['id'=>$meal->id,'quantity'=>1,'notes'=>'extra salt']
            ],
            'selected_delivery_time'=>$faker->dateTimeBetween(Carbon::create($chef->delivery_ends_at)->subHour(),Carbon::create($chef->delivery_ends_at))
            ->format('Y-m-d H:i:s'),
            'notes'=>'extra extra',
            'total_cost'=>'15722',
            'meals_cost'=>'11000',
            'payment_method'=>'cash'
         ]
        );
         $response->assertStatus(201);
       
    }
    /**
     * make an invalid order 
     * the selected deivery time is out range the chef schedule
     */
    public function test_make_invalid_order(){
       
        $faker = \Faker\Factory::create();
        $category=Category::factory()->create();
        $location=Location::factory()->create();
        $user = User::factory()->create([
            'location_id' => 1,
        ]);
        $chef=Chef::factory()->create([
            'is_available'=>true,
            'location_id'=>$location->id,
            'delivery_starts_at'=>now()->subHours(2),
            'delivery_ends_at'=>now()->addHours(2),
        ]);
        $meal=Meal::factory()->create([
            'chef_id'=>$chef->id,
            'category_id'=>$category->id,
            'is_available'=>true
        ]);
        $response = $this->actingAs($user,'user')
                         ->postJson('/api/user/make-order',
         [
            'chef_id' => $chef->id,
            'meals_count'=>1,
            'meals'=>[
                ['id'=>$meal->id,'quantity'=>1,'notes'=>'extra salt']
            ],
            'selected_delivery_time'=>$faker->dateTimeBetween($chef->delivery_ends_at,Carbon::create($chef->delivery_starts_at)->addDay())
            ->format('Y-m-d H:i:s'),
            'notes'=>'extra extra',
            'total_cost'=>'15722',
            'meals_cost'=>'11000',
            'payment_method'=>'cash'
         ]
        );
         $response->assertStatus(422);
         $response->assertSeeText('\u0627\u0644\u0648\u0642\u062a \u0627\u0644\u0645\u062d\u062f\u062f \u0644\u0644\u062a\u0648\u0635\u064a\u0644 \u064a\u062c\u0628 \u0623\u0646 \u064a\u0643\u0648\u0646 \u0636\u0645\u0646 \u0627\u0644\u0623\u0648\u0642\u0627\u062a \u0627\u0644\u0645\u062a\u0627\u062d\u0629 \u0644\u0644\u062a\u0648\u0635\u064a\u0644');
       
    }
    /**
     * make an invalid order 
     * the ordered dmeal quantity is more than the max accepted number
     */
    public function test_make_bad_order_bad_meal_quantity(){
       
        $faker = \Faker\Factory::create();
        $category=Category::factory()->create();
        $location=Location::factory()->create();
        $user = User::factory()->create([
            'location_id' => 1,
        ]);
        $chef=Chef::factory()->create([
            'is_available'=>true,
            'location_id'=>$location->id,
            'delivery_starts_at'=>now()->subHours(2),
            'delivery_ends_at'=>now()->addHours(2),
        ]);
        $meal=Meal::factory()->create([
            'chef_id'=>$chef->id,
            'category_id'=>$category->id,
            'is_available'=>true,
            'max_meals_per_day'=>2
        ]);
        $response = $this->actingAs($user,'user')
                         ->postJson('/api/user/make-order',
         [
            'chef_id' => $chef->id,
            'meals_count'=>1,
            'meals'=>[
                ['id'=>$meal->id,'quantity'=>3,'notes'=>'extra salt']
            ],
            'selected_delivery_time'=>$faker->dateTimeBetween(Carbon::create($chef->delivery_ends_at)->subHour(),$chef->delivery_ends_at)
            ->format('Y-m-d H:i:s'),
            'notes'=>'extra extra',
            'total_cost'=>'15722',
            'meals_cost'=>'11000',
            'payment_method'=>'cash'
         ]
        );
         $response->assertStatus(400);
         $response->assertSeeText('\u0627\u0644\u0623\u0642\u0635\u0649 \u0645\u0646 \u0627\u0644\u0639\u062f\u062f \u0627\u0644\u0645\u0633\u0645\u0648\u062d \u0644\u0637\u0644\u0628\u0647\u0627 \u0627\u0644\u064a\u0648\u0645');
       
    }
     /**
     * make an invalid order 
     * the ordered dmeal quantity is more than the max orders for chef
     */
    public function test_make_bad_order_bad_chef_meal_quantity(){
       
        $faker = \Faker\Factory::create();
        $category=Category::factory()->create();
        $location=Location::factory()->create();
        $user = User::factory()->create([
            'location_id' => 1,
        ]);
        $chef=Chef::factory()->create([
            'is_available'=>true,
            'location_id'=>$location->id,
            'max_meals_per_day'=>2,
            'delivery_starts_at'=>now()->subHours(2),
            'delivery_ends_at'=>now()->addHours(2),
        ]);
        $meal=Meal::factory()->create([
            'chef_id'=>$chef->id,
            'category_id'=>$category->id,
            'is_available'=>true,
        ]);
        $response = $this->actingAs($user,'user')
                         ->postJson('/api/user/make-order',
         [
            'chef_id' => $chef->id,
            'meals_count'=>1,
            'meals'=>[
                ['id'=>$meal->id,'quantity'=>3,'notes'=>'extra salt']
            ],
            'selected_delivery_time'=>$faker->dateTimeBetween(Carbon::create($chef->delivery_ends_at)->subHour(),$chef->delivery_ends_at)
            ->format('Y-m-d H:i:s'),
            'notes'=>'extra extra',
            'total_cost'=>'15722',
            'meals_cost'=>'11000',
            'payment_method'=>'cash'
         ]
        );
         $response->assertStatus(400);
         $response->assertSeeText('\u0644\u0627 \u064a\u0645\u0643\u0646 \u0627\u0644\u0637\u0644\u0628 \u0645\u0646 \u0647\u0630\u0627 \u0627\u0644\u0637\u0627\u0647\u064a \u0644\u0623\u0646\u0647 \u0648\u0635\u0644 \u0625\u0644\u0649 \u0627\u0644\u062d\u062f \u0627\u0644\u0623\u0642\u0635\u0649 \u0645\u0646 \u0627\u0644\u0637\u0644\u0628\u0627\u062a');
       
    }
    /**
     * make an invalid order 
     * the order includes an inavailable meal
     */
    public function test_make_bad_order_inavailable_meal(){
       
        $faker = \Faker\Factory::create();
        $category=Category::factory()->create();
        $location=Location::factory()->create();
        $user = User::factory()->create([
            'location_id' => 1,
        ]);
        $chef=Chef::factory()->create([
            'is_available'=>true,
            'location_id'=>$location->id,
            'delivery_starts_at'=>now()->subHours(2),
            'delivery_ends_at'=>now()->addHours(2),
        ]);
        $meal=Meal::factory()->create([
            'chef_id'=>$chef->id,
            'category_id'=>$category->id,
            'is_available'=>false,
        ]);
        $response = $this->actingAs($user,'user')
                         ->postJson('/api/user/make-order',
         [
            'chef_id' => $chef->id,
            'meals_count'=>1,
            'meals'=>[
                ['id'=>$meal->id,'quantity'=>1,'notes'=>'extra salt']
            ],
            'selected_delivery_time'=>$faker->dateTimeBetween(Carbon::create($chef->delivery_ends_at)->subHour(),$chef->delivery_ends_at)
            ->format('Y-m-d H:i:s'),
            'notes'=>'extra extra',
            'total_cost'=>'15722',
            'meals_cost'=>'11000',
            'payment_method'=>'cash'
         ]
        );
         $response->assertStatus(400);
         $response->assertSeeText('\u062a\u0648\u062c\u062f \u0648\u062c\u0628\u0629 \u063a\u064a\u0631 \u0645\u062a\u0627\u062d\u0629 \u0644\u0644\u0637\u0644\u0628');
       
    }
}
