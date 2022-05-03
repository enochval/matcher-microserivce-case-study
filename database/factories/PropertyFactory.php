<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class PropertyFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $fields = array_map('strval', [
            'area' => $this->faker->numberBetween(100, 900),
            'yearOfConstruction' => $this->faker->numberBetween(2005, 2022),
            'rooms' => $this->faker->numberBetween(1, 15),
            'heatingType' => $this->faker->randomElement(['gas', 'electric']),
            'parking' => (boolean) $this->faker->randomElement([true, false]),
            'returnActual' => $this->faker->randomFloat(1, 10.5, 90.5),
            'price' => $this->faker->numberBetween(100000, 9999999)
        ]);
        $fields['parking'] = (boolean) $fields['parking'];

        return [
            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'property_type' => Uuid::uuid4()->toString(),
            'fields' => $fields
        ];
    }
}

