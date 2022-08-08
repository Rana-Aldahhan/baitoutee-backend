<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;


class ExampleTest extends TestCase
{
    use DatabaseTransactions,WithFaker;
    /**
     * test if top rated meals to user returns ok
     */
    public function test_top_rated_meals(){
        $user = User::factory()->create();
        $response = $this->actingAs($user,'user')
                         ->get('/api/user/get-top-rated-meals')
                         ->assertSuccessful();
        
    }
    
}
