<?php namespace RainLab\Location\Updates;

use October\Rain\Database\Updates\Seeder;
use RainLab\Location\Models\Country;

class SeedAtStates extends Seeder
{
    public function run()
    {
        $country = Country::whereCode('AT')->first();
        if ($country->states()->count() > 0) {
            return;
        }

        $country->states()->createMany([
            ['code' => 'WI', 'name' => 'Wien'],
            ['code' => 'NI', 'name' => 'Niederösterreich'],
            ['code' => 'OB', 'name' => 'Oberösterreich'],
            ['code' => 'ST', 'name' => 'Steiermark'],
            ['code' => 'TI', 'name' => 'Tirol'],
            ['code' => 'KA', 'name' => 'Kärnten'],
            ['code' => 'SA', 'name' => 'Salzburg'],
            ['code' => 'VO', 'name' => 'Vorarlberg'],
            ['code' => 'BU', 'name' => 'Burgenland']
        ]);
    }
}
