<?php

namespace Tests\Unit;

use App\Http\Controllers\MatcherController;
use App\Models\Property;
use App\Traits\PropertyTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class MatcherTest extends TestCase
{
    use RefreshDatabase;
    use PropertyTrait;

    protected $seed = true;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_not_found_property_returns_null()
    {
        $matcher = (new MatcherController())
            ->matchPropertyToSearchProfiles(Property::find(Property::count() + 1));
        $this->assertEquals(null, $matcher);
    }

    public function test_search_profile_matches_returns_an_array_of_search_profiles()
    {
        $matcher = $this->searchProfileMatches(Property::first()->fields);
        $this->assertIsArray($matcher);
    }

    public function test_search_profile_fields_are_resolved_into_strict_and_loose_match_based_on_property_fields()
    {
        $property = Property::find(1);
        $matched = $this->searchProfileMatches($property->fields);
        $resolver = $this->resolveMatches($property, Arr::first($matched));
        $this->assertEquals([
            "score" => 12,
            "strictMatchesCount" => 4,
            "looseMatchesCount" => 0
        ], $resolver);
    }

    public function test_searched_field_is_strict_match_to_property_field_when_direct_values_equals()
    {
        $property_field = "180";
        $searched_field_value = "180";
        $is_strict_match = $this->isStrictMatch($property_field, $searched_field_value);
        $this->assertTrue($is_strict_match);
    }

    public function test_strict_match_when_property_value_is_in_range_of_search_profile_value()
    {
        $property_field = "1500000";
        $searched_field_value = ["0", "2000000"];
        $is_strict_match = $this->isStrictMatch($property_field, $searched_field_value);
        $this->assertTrue($is_strict_match);
    }

    public function test_loose_match_when_deviated_is_applied_to_a_range_of_search_profile_value()
    {
        $property_field = "230";
        $searched_field_value = ["100", "200"];
        $is_loose_match = $this->isLooseMatch($property_field, $searched_field_value);
        $this->assertTrue($is_loose_match);
    }

    public function test_deviated_calculation_returns_25_percent_below_the_value()
    {
        $value = 100;
        $cals = abs($this->applyDeviatedPercentage($value, false));
        $this->assertEquals(75, $cals);
    }

    public function test_deviated_calculation_returns_25_percent_above_the_value()
    {
        $value = 100;
        $cals = abs($this->applyDeviatedPercentage($value, true));
        $this->assertEquals(125, $cals);
    }

    public function test_sort_array_by_score_returns_sorting_from_highest_to_lowest_value()
    {
        $scores = [
            ['score' => 15, 'id' => 1],
            ['score' => 200, 'id' => 2],
            ['score' => 90, 'id' => 3],
            ['score' => 55, 'id' => 4],
            ['score' => 350, 'id' => 5]
        ];

        $sort = $this->sortArrayBy($scores, 'score');

        $this->assertEquals([
            ['score' => 350, 'id' => 5],
            ['score' => 200, 'id' => 2],
            ['score' => 90, 'id' => 3],
            ['score' => 55, 'id' => 4],
            ['score' => 15, 'id' => 1],
        ], $sort);
    }
}
