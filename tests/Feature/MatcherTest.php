<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MatcherTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_not_found_property_returns_404()
    {
        $property = Property::count() + 1;
        $response = $this->get("/api/match/$property");

        $response->assertStatus(404);
    }

    public function test_status_code_returns_200_for_valid_property_and_contains_required_response_properties()
    {
        $property = Property::first();
        $response = $this->get("/api/match/$property->id");
        $response
            ->assertStatus(200)
            ->assertJson(function ($json) {
            $json->has('data')
                ->has('data.0.searchProfileId')
                ->has('data.0.score')
                ->has('data.0.strictMatchesCount')
                ->has('data.0.looseMatchesCount');
        });
    }
}
