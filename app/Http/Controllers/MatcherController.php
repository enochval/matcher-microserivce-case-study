<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Traits\PropertyTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class MatcherController extends Controller
{
    use PropertyTrait;

    public function __construct()
    {
        //
    }

    /**
     * @param Property|null $property
     * @return JsonResource
     */
    public function matchPropertyToSearchProfiles(?Property $property): ?JsonResource
    {
        if (!$property) {
            return null;
        }
        // get matched search profiles
        $matched_search_profiles = $this->searchProfileMatches($property->fields);

        // handles the matching of property to search profiles
        $response = $this->handleMatchingProperty($property, $matched_search_profiles);

        return new JsonResource($response);
    }
}
