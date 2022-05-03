<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\SearchProfile;
use Illuminate\Database\Seeder;

class MatcherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Property::create([
            'name' => 'Awesome house in the middle of my town',
            'address' => 'Main street 17, 12456 Berlin',
            'property_type' => 'd44d0090-a2b5-47f7-80bb-d6e6f85fca90',
            'fields' => [
                'area' => '180',
                'yearOfConstruction' => '2010',
                'rooms' => '5',
                'heatingType' => 'gas',
                'parking' => true,
                'returnActual' => '12.8',
                'price' => '1500000'
            ],
        ]);
        Property::factory()->count(5)->create();

        SearchProfile::create([
            'name' => 'Looking for any Awesome realestate!',
            'property_type' => 'd44d0090-a2b5-47f7-80bb-d6e6f85fca90',
            'search_fields' => [
                'price' => ['0', '2000000'],
                'area' => ['150', null],
                'yearOfConstruction' => ['2010', null],
                'rooms' => ['4', null],
            ],
            'return_potential' => ['15', null],
        ]);
        SearchProfile::factory()->count(15)->create();
    }
}
