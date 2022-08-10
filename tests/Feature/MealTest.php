<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Chef;
use App\Models\Location;
use App\Models\Meal;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Faker\Provider\Image;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;

class MealTest extends TestCase
{

    use DatabaseTransactions,WithFaker;
    /**
     * test adding a successful meal
     */
    public function test_make_successful_meal(){
        $knownDate = Carbon::create(2022, 8, 9, 12);
        Carbon::setTestNow($knownDate);
        // fake a storage to store image in it
        Storage::fake('public');
        $category=Category::factory()->create();
        $location=Location::factory()->create();
        $chef=Chef::factory()->create([
            'is_available'=>true,
            'location_id'=>$location->id,
            'max_meals_per_day'=>10,
            'delivery_starts_at'=>now()->subHours(2),
            'delivery_ends_at'=>now()->addHours(2),
        ]);
        $file = File::create('meal.jpg',100);
        //used a normal file instead of image cause using the instruction below
        //UploadedFile::fake()->image(uniqid() . '.jpg')
        // require to download php_gd2.dll and add ;extension=php_gd2.dll to the ini file
        // cause my php version does not support making image colors automatecially
        $response = $this->actingAs($chef,'chef')
                         ->postJson('/api/chef/meals/',
                        ['image'=> $file,
                        'category_id'=> $category->id,
                        'name' => 'وجبة للاختبار',
                        'price'=>$this->faker->numberBetween(500,20000),
                        'max_meals_per_day'=>3,
                        'expected_preparation_time'=>$this->faker->numberBetween(10,120),
                        'ingredients'=>$this->faker->sentence(20)]);

                        $response->assertStatus(201);
            // didn't know how to get the id of the created meal so create a meal using factory and get id-1
            // this cant run with other tests
           $meal = Meal::factory()->create([
            'chef_id'=> $chef->id,
            'category_id'=>$category->id,
           ]);
           //check if the image of the meal is stored on the disk
           $testMeal = Meal::find($meal->id-1);
           //dd($testMeal->image);
           $this->assertNotNull($testMeal->image);
           Storage::disk('public')->assertExists($testMeal->image);
           $this->assertFileEquals($file,Storage::disk('public')->path($testMeal->image));

    }
    public function test_meal_invalid_discount(){
        $knownDate = Carbon::create(2022, 8, 9, 12);
        Carbon::setTestNow($knownDate);

        $category=Category::factory()->create();
        $location=Location::factory()->create();
        $chef=Chef::factory()->create([
            'is_available'=>true,
            'location_id'=>$location->id,
            'max_meals_per_day'=>10,
            'delivery_starts_at'=>now()->subHours(2),
            'delivery_ends_at'=>now()->addHours(2),
        ]);
        $file = File::create('meal.jpg',100);

        $response = $this->actingAs($chef,'chef')
                         ->postJson('/api/chef/meals/',
                        ['image'=> $file,
                        'category_id'=> $category->id,
                        'name' => 'وجبة للاختبار',
                        'price'=> intval(1000),
                        'max_meals_per_day'=>3,
                        'expected_preparation_time'=>$this->faker->numberBetween(10,120),
                        'ingredients'=>$this->faker->sentence(20),
                        'discount_percentage' => intval(1000),
                    ]);
           $response->assertStatus(422);
           $response->assertSeeText(' \u0646\u0633\u0628\u0629 \u0627\u0644\u062e\u0635\u0645 \u0639\u0644\u0649 \u0627\u0644\u0633\u0639\u0631 \u064a\u062c\u0628 \u0623\u0644\u0627 \u062a\u0643\u0648\u0646 \u0623\u0643\u0628\u0631 \u0645\u0646  100.');

    }
    public function test_meal_invalid_price(){
        $knownDate = Carbon::create(2022, 8, 9, 12);
        Carbon::setTestNow($knownDate);

        $category=Category::factory()->create();
        $location=Location::factory()->create();
        $chef=Chef::factory()->create([
            'is_available'=>true,
            'location_id'=>$location->id,
            'max_meals_per_day'=>10,
            'delivery_starts_at'=>now()->subHours(2),
            'delivery_ends_at'=>now()->addHours(2),
        ]);
        $file = File::create('meal.jpg',100);
        $response = $this->actingAs($chef,'chef')
                         ->postJson('/api/chef/meals/',
                        ['image'=> $file,
                        'category_id'=> $category->id,
                        'name' => 'وجبة للاختبار',
                        'price'=> intval(-200),
                        'max_meals_per_day'=>3,
                        'expected_preparation_time'=>$this->faker->numberBetween(10,120),
                        'ingredients'=>$this->faker->sentence(20),
                    ]);
           $response->assertStatus(422);
           $response->assertSeeText(' \u0627\u0644\u0633\u0639\u0631 \u064a\u062c\u0628 \u0623\u0646 \u064a\u0643\u0648\u0646 \u0639\u0644\u0649 \u0627\u0644\u0623\u0642\u0644 1');

    }
    public function test_meal_invalid_max_per_day(){
        $knownDate = Carbon::create(2022, 8, 9, 12);
        Carbon::setTestNow($knownDate);
        $category=Category::factory()->create();
        $location=Location::factory()->create();
        $chef=Chef::factory()->create([
            'is_available'=>true,
            'location_id'=>$location->id,
            'max_meals_per_day'=>10,
            'delivery_starts_at'=>now()->subHours(2),
            'delivery_ends_at'=>now()->addHours(2),
        ]);
        $file = File::create('meal.jpg',100);
        $response = $this->actingAs($chef,'chef')
                         ->postJson('/api/chef/meals/',
                        ['image'=> $file,
                        'category_id'=> $category->id,
                        'name' => 'وجبة للاختبار',
                        'price'=> intval(2000),
                        'max_meals_per_day'=>100,
                        'expected_preparation_time'=>$this->faker->numberBetween(10,120),
                        'ingredients'=>$this->faker->sentence(20),
                    ]);
           $response->assertStatus(422);
           $response->assertSeeText(' \u0627\u0644\u0639\u062f\u062f \u0627\u0644\u0623\u0639\u0638\u0645\u064a \u064a\u062c\u0628 \u0623\u0646 \u064a\u0643\u0648\u0646 \u0623\u0635\u063a\u0631 \u0645\u0646 \u0639\u062f\u062f \u0627\u0644\u0648\u062c\u0628\u0627\u062a \u0627\u0644\u0643\u0644\u064a \u0627\u0644\u0645\u0645\u0643\u0646 \u062a\u062d\u0636\u064a\u0631\u0647  ');
    }

    public function test_meal_blank_image(){
        $knownDate = Carbon::create(2022, 8, 9, 12);
        Carbon::setTestNow($knownDate);
        $category=Category::factory()->create();
        $location=Location::factory()->create();
        $chef=Chef::factory()->create([
            'is_available'=>true,
            'location_id'=>$location->id,
            'max_meals_per_day'=>10,
            'delivery_starts_at'=>now()->subHours(2),
            'delivery_ends_at'=>now()->addHours(2),
        ]);

        $response = $this->actingAs($chef,'chef')
                         ->postJson('/api/chef/meals/',
                        [
                        'category_id'=> $category->id,
                        'name' => 'وجبة للاختبار',
                        'price'=> intval(2000),
                        'max_meals_per_day'=>100,
                        'expected_preparation_time'=>$this->faker->numberBetween(10,120),
                        'ingredients'=>$this->faker->sentence(20),
                    ]);
           $response->assertStatus(422);
           $response->assertSeeText(' \u0627\u0644\u0635\u0648\u0631\u0629 \u0647\u0648 \u062d\u0642\u0644 \u0645\u0637\u0644\u0648\u0628.');
        }

}
