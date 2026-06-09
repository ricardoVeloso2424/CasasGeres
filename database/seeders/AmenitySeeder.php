<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Wi-Fi', 'icon' => 'wifi'],
            ['name' => 'Cozinha equipada', 'icon' => 'utensils'],
            ['name' => 'Estacionamento', 'icon' => 'parking'],
            ['name' => 'Aquecimento', 'icon' => 'heater'],
            ['name' => 'Ar condicionado', 'icon' => 'snowflake'],
            ['name' => 'Varanda ou terraço', 'icon' => 'sun'],
            ['name' => 'Churrasqueira', 'icon' => 'flame'],
            ['name' => 'Roupa de cama e toalhas', 'icon' => 'bed'],
        ])->each(function (array $amenity): void {
            Amenity::query()->updateOrCreate(
                ['slug' => Str::slug($amenity['name'])],
                $amenity + ['slug' => Str::slug($amenity['name'])]
            );
        });
    }
}
