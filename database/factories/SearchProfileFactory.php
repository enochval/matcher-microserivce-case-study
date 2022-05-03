<?php

namespace Database\Factories;

use App\Models\SearchProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class SearchProfileFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = SearchProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'property_type' => Uuid::uuid4()->toString(),
            'search_fields' => [
                'price' => $this->generateField($this->faker->numberBetween(100000, 9999999), $this->faker->numberBetween(100000, 9999999)),
                'area' => $this->generateField($this->faker->numberBetween(100, 900), $this->faker->numberBetween(100, 900)),
                'yearOfConstruction' => $this->generateField($this->faker->numberBetween(2005, 2022), $this->faker->numberBetween(2005, 2022)),
                'rooms' => $this->generateField($this->faker->numberBetween(1, 15), $this->faker->numberBetween(1, 15)),
                'heatingType' => $this->generateField($this->faker->randomElement(['gas', 'electric']), $this->faker->randomElement(['gas', 'electric'])),
                'parking' => $this->generateField($this->faker->randomElement([true, false]), $this->faker->randomElement([true, false])),
            ],
            'return_potential' => $this->generateField($this->faker->randomFloat(1, 10.5, 90.5), $this->faker->randomFloat(1, 10.5, 90.5))
        ];
    }

    public function generateField($first_value, $second_value): array
    {
        $field_range = [
            $this->faker->randomElement([$first_value, null]),
            $this->faker->randomElement([$second_value, null]),
        ];

        if (!in_array(null, $field_range, true)) {
            asort($field_range);
            $field_range = array_values($field_range);
        }
        return array_map(function ($field_value) {
            return is_null($field_value) ? null : (is_bool($field_value) ? $field_value : strval($field_value));
        }, $field_range);
    }
}
