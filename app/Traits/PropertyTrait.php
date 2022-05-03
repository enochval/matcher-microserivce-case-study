<?php


namespace App\Traits;

use App\Models\Property;
use App\Models\SearchProfile;

trait PropertyTrait
{
    /**
     * @param Property $property
     * @param $matched_search_profiles
     * @return array
     */
    public function handleMatchingProperty(Property $property, array $matched_search_profiles): array
    {
        $response = array_map(function ($search_profile) use ($property) {
            // determines the match type and attempt to categorize between direct and range
            // and of the dirty work...
            $result = $this->resolveMatches($property, $search_profile);

            return [
                'searchProfileId' => $search_profile->id,
                'score' => $result['score'],
                'strictMatchesCount' => $result['strictMatchesCount'],
                'looseMatchesCount' => $result['looseMatchesCount'],
            ];
        }, $matched_search_profiles);

        // sort the result based on the criteria, in this case it's the score
        return $this->sortArrayBy($response, 'score');
    }

    /**
     * @param array $property_fields
     * @return array
     */
    public function searchProfileMatches(array $property_fields): array
    {
        // initialized search profile query builder.
        $query = SearchProfile::query();

        foreach ($property_fields as $field_key => $field_value) {
            // determining is a value is present.
            if (!isset($field_value)) {
                continue;
            }

            // resolve the column based on the field_key
            $column = ($field_key === 'returnActual') ? 'return_potential' : 'search_fields->' . $field_key;

            // add a query entry to the searchprofile query builder instance
            $query = $query->orWhereJsonContains($column, [$field_value]);
        }

        // get all search profile result
        return $query->get()->all();
    }

    /**
     * @param Property $property
     * @param SearchProfile $search_profile
     * @return array
     */
    private function resolveMatches(Property $property, SearchProfile $search_profile): array
    {
        $strict_match_count = 0;
        $loose_match_count = 0;

        $property_fields = $property->fields;
        $search_fields = optional($search_profile)->search_fields ?? [];

        foreach ($search_fields as $field_key => $field_value) {

            // determining if property value is provided.
            if (!isset($property_fields[$field_key])) {
                continue;
            }

            // determining if this is a strict match, then increment the strict match count.
            if ($str = $this->isStrictMatch($property_fields[$field_key], $field_value)) {
                $strict_match_count++;
                continue;
            }

            // determining if this is a loose match, then increment the loose match count
            if ($this->isLooseMatch($property_fields[$field_key], $field_value)) {
                $loose_match_count++;
            }
        }

        // return result
        return [
            // A higher score to represent higher matching relevance
            'score' => (3 * $strict_match_count) + ($loose_match_count),
            'strictMatchesCount' => $strict_match_count,
            'looseMatchesCount' => $loose_match_count,
        ];
    }

    /**
     * @param $property_value
     * @param $search_profile_value
     * @return bool
     */
    private function isStrictMatch($property_value, $search_profile_value): bool
    {
        // for direct field type
        if (!is_array($search_profile_value)) {
            if ($search_profile_value === null) {
                return true;
            }
            return $property_value === $search_profile_value;
        }

        // for range field type
        [$min, $max] = $search_profile_value;

        if (is_bool($min) && is_bool($max)) {
            return in_array($property_value, $search_profile_value);
        }

        return (is_null($min) || $property_value >= $min) &&
            (is_null($max) || $property_value <= $max);
    }

    /**
     * @param $property_value
     * @param $search_profile_value
     * @return bool
     */
    private function isLooseMatch($property_value, $search_profile_value): bool
    {
        // return false already
        if (!is_array($search_profile_value)) {
            return false;
        }

        // for range loose type
        [$min, $max] = $search_profile_value;

        if (is_bool($min) || is_bool($max)) {
            return false;
        }

        // for range field type
        // check if property value is within range of deviated percentage
        return (is_null($min) || $property_value >= $this->applyDeviatedPercentage((int)$min)) &&
            (is_null($max) || $property_value <= $this->applyDeviatedPercentage((int)$max, true));
    }

    /**
     * @param $value
     * @param false $increment
     * @return int
     */
    private function applyDeviatedPercentage($value, $increment = false): int
    {
        if ($increment) {
            return (int)((0.25 * $value) + $value);
        }
        return (int)((0.25 * $value) - $value);
    }

    /**
     * @param $data
     * @param $sort_by
     * @return mixed
     */
    public function sortArrayBy($data, $sort_by)
    {
        usort($data, function ($a, $b) use ($sort_by) {
            if ($a[$sort_by] == $b[$sort_by]) {
                return 0;
            }
            return ($a[$sort_by] < $b[$sort_by]) ? 1 : -1;
        });

        return $data;
    }
}
