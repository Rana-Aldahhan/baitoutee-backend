<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Chef;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

class ExampleTest extends TestCase
{
    use DatabaseTransactions,WithFaker;

    // public function __construct()
    // {
    //     $this->setUpFaker();
    // }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    /**
     * test if top rated meals to user returns ok
     */
    public function test_top_rated_meals(){
        $user = User::factory()->create();
        $response = $this->actingAs($user,'user')
                         ->get('/api/user/get-top-rated-meals')
                         ->assertSuccessful();
        
    }
    /**
     * make a successful order:
     * this test requires a chef with id of 3 who has a meal with an id of 6
     * both the chef and the meal must be available && has at least one active orders number
     * future edit:make factories of chefs and meals and use it instead
     */
    public function test_make_successful_order(){
       
        $faker = \Faker\Factory::create();
        $user = User::factory()->create([
            'location_id' => 1,
        ]);
        $response = $this->actingAs($user,'user')
                         ->postJson('/api/user/make-order',
         [
            'chef_id' => 3,
            'meals_count'=>1,
            'meals'=>[
                ['id'=>6,'quantity'=>1,'notes'=>'extra salt']
            ],
            'selected_delivery_time'=>$faker->dateTimeBetween(Chef::find(1)->delivery_starts_at,Chef::find(1)->delivery_ends_at)
            ->format('Y-m-d H:i:s'),
            'notes'=>'extra extra',
            'total_cost'=>'15722',
            'meals_cost'=>'11000'
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
        $user = User::factory()->create([
            'location_id' => 1,
        ]);
        $response = $this->actingAs($user,'user')
                         ->postJson('/api/user/make-order',
         [
            'chef_id' => 3,
            'meals_count'=>1,
            'meals'=>[
                ['id'=>6,'quantity'=>1,'notes'=>'extra salt']
            ],
            'selected_delivery_time'=>$faker->dateTimeBetween(Chef::find(1)->delivery_ends_at,Carbon::create(Chef::find(1)->delivery_starts_at)->addDay())
            ->format('Y-m-d H:i:s'),
            'notes'=>'extra extra',
            'total_cost'=>'15722',
            'meals_cost'=>'11000'
         ]
        );
         $response->assertStatus(422);
       
    }
}
