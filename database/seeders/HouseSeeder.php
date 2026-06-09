<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\House;
use Illuminate\Database\Seeder;

class HouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = Amenity::query()->pluck('id', 'slug');

        $houseOne = House::query()->updateOrCreate(
            ['slug' => 'casa-do-rio'],
            [
                'name' => 'Casa do Rio',
                'short_description' => 'Casa familiar dividida em três T1 independentes, junto à natureza.',
                'description' => 'A Casa do Rio foi pensada para estadias tranquilas no Gerês, com unidades independentes, zonas exteriores e acesso fácil a trilhos, cascatas e miradouros. É uma boa opção para famílias ou grupos que querem ficar próximos, mantendo privacidade.',
                'location' => 'Vilar da Veiga, Gerês',
                'address_optional' => 'Zona próxima da albufeira da Caniçada',
                'latitude' => 41.7112410,
                'longitude' => -8.7216040,
                'is_active' => true,
                'featured' => true,
            ]
        );

        $houseOne->photos()->delete();
        $houseOne->photos()->createMany([
            [
                'path' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1400&q=80',
                'alt' => 'Casa acolhedora rodeada por natureza no Gerês',
                'sort_order' => 1,
                'is_cover' => true,
            ],
            [
                'path' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1400&q=80',
                'alt' => 'Exterior de alojamento local com jardim',
                'sort_order' => 2,
                'is_cover' => false,
            ],
        ]);

        $t1A = $houseOne->rentalUnits()->updateOrCreate(
            ['slug' => 't1-a'],
            [
                'name' => 'T1 A',
                'type' => 'T1',
                'short_description' => 'Unidade T1 independente para uma escapadinha a dois.',
                'description' => 'O T1 A tem quarto, sala com kitchenette e uma zona exterior simples para descansar depois de um dia de trilhos.',
                'capacity' => 2,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'base_price' => 75,
                'rules' => 'Não é permitido fumar no interior. Respeitar o silêncio a partir das 22h.',
                'is_active' => true,
                'featured' => true,
            ]
        );

        $t1B = $houseOne->rentalUnits()->updateOrCreate(
            ['slug' => 't1-b'],
            [
                'name' => 'T1 B',
                'type' => 'T1',
                'short_description' => 'T1 confortável para casal ou pequena família.',
                'description' => 'Unidade prática, luminosa e equipada para estadias curtas ou prolongadas, com ligação rápida aos principais pontos do Gerês.',
                'capacity' => 3,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'base_price' => 82,
                'rules' => 'Não é permitido fumar no interior. Animais sujeitos a confirmação.',
                'is_active' => true,
                'featured' => false,
            ]
        );

        $t1C = $houseOne->rentalUnits()->updateOrCreate(
            ['slug' => 't1-c'],
            [
                'name' => 'T1 C',
                'type' => 'T1',
                'short_description' => 'Unidade independente com ambiente calmo e vista verde.',
                'description' => 'Ideal para descansar no Gerês com privacidade, cozinha equipada e acesso às zonas exteriores da casa.',
                'capacity' => 2,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'base_price' => 78,
                'rules' => 'Não é permitido fumar no interior. Check-in mediante contacto prévio.',
                'is_active' => true,
                'featured' => false,
            ]
        );

        $houseTwo = House::query()->updateOrCreate(
            ['slug' => 'casa-da-encosta'],
            [
                'name' => 'Casa da Encosta',
                'short_description' => 'Alojamento com um T2 e um T1, pensado para famílias e amigos.',
                'description' => 'A Casa da Encosta combina conforto e proximidade à natureza. As unidades podem ser reservadas separadamente, permitindo receber casais, famílias pequenas ou grupos que viajam juntos.',
                'location' => 'Rio Caldo, Gerês',
                'address_optional' => 'Perto da marina e da Caniçada',
                'latitude' => 41.6768360,
                'longitude' => -8.1963330,
                'is_active' => true,
                'featured' => true,
            ]
        );

        $houseTwo->photos()->delete();
        $houseTwo->photos()->createMany([
            [
                'path' => 'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1400&q=80',
                'alt' => 'Casa de campo com árvores e montanha',
                'sort_order' => 1,
                'is_cover' => true,
            ],
        ]);

        $t2 = $houseTwo->rentalUnits()->updateOrCreate(
            ['slug' => 't2-familia'],
            [
                'name' => 'T2 Família',
                'type' => 'T2',
                'short_description' => 'Unidade T2 para famílias que querem espaço e autonomia.',
                'description' => 'Com dois quartos, sala comum e cozinha equipada, o T2 Família é uma base confortável para explorar o Gerês em ritmo calmo.',
                'capacity' => 5,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'base_price' => 120,
                'rules' => 'Reserva sujeita a confirmação. Respeitar as zonas comuns.',
                'is_active' => true,
                'featured' => true,
            ]
        );

        $t1Encosta = $houseTwo->rentalUnits()->updateOrCreate(
            ['slug' => 't1-encosta'],
            [
                'name' => 'T1 Encosta',
                'type' => 'T1',
                'short_description' => 'T1 simples e acolhedor numa zona tranquila.',
                'description' => 'Uma unidade compacta, equipada e confortável para estadias de casal, com acesso fácil a restaurantes, trilhos e miradouros.',
                'capacity' => 2,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'base_price' => 80,
                'rules' => 'Não é permitido fumar no interior. Check-out até às 11h.',
                'is_active' => true,
                'featured' => false,
            ]
        );

        $houseThree = House::query()->updateOrCreate(
            ['slug' => 'casa-do-carvalho'],
            [
                'name' => 'Casa do Carvalho',
                'short_description' => 'Casa inteira para grupos e famílias, com ambiente reservado.',
                'description' => 'A Casa do Carvalho funciona como unidade inteira, ideal para quem quer juntar família ou amigos numa estadia mais privada. Tem áreas comuns amplas e uma envolvente natural perfeita para desligar.',
                'location' => 'Terras de Bouro, Gerês',
                'address_optional' => 'Zona rural com bons acessos',
                'latitude' => 41.7241050,
                'longitude' => -8.3114820,
                'is_active' => true,
                'featured' => true,
            ]
        );

        $houseThree->photos()->delete();
        $houseThree->photos()->createMany([
            [
                'path' => 'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1400&q=80',
                'alt' => 'Casa inteira com jardim para férias em família',
                'sort_order' => 1,
                'is_cover' => true,
            ],
        ]);

        $entireHouse = $houseThree->rentalUnits()->updateOrCreate(
            ['slug' => 'casa-inteira'],
            [
                'name' => 'Casa Inteira',
                'type' => 'Casa inteira',
                'short_description' => 'Casa completa para estadias em grupo com privacidade.',
                'description' => 'A unidade inclui quartos, cozinha equipada, zona de refeição e espaço exterior. É indicada para famílias maiores ou grupos que valorizam independência.',
                'capacity' => 8,
                'bedrooms' => 4,
                'bathrooms' => 2,
                'base_price' => 210,
                'rules' => 'Eventos sujeitos a autorização. Reserva apenas confirmada após contacto direto.',
                'is_active' => true,
                'featured' => true,
            ]
        );

        $allUnits = collect([$t1A, $t1B, $t1C, $t2, $t1Encosta, $entireHouse]);
        $defaultAmenityIds = $amenities->only([
            'wi-fi',
            'cozinha-equipada',
            'estacionamento',
            'aquecimento',
            'roupa-de-cama-e-toalhas',
        ])->values()->all();

        $allUnits->each(function ($unit) use ($defaultAmenityIds): void {
            $unit->amenities()->sync($defaultAmenityIds);

            $unit->photos()->delete();
            $unit->photos()->create([
                'path' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80',
                'alt' => "{$unit->name} preparado para receber hóspedes",
                'sort_order' => 1,
                'is_cover' => true,
            ]);

            $unit->calendarSources()->delete();
            $bookingCalendar = $unit->calendarSources()->create([
                'platform' => 'Booking',
                'ical_url' => "https://example.com/calendars/booking/{$unit->slug}.ics",
                'is_active' => true,
            ]);
            $airbnbCalendar = $unit->calendarSources()->create([
                'platform' => 'Airbnb',
                'ical_url' => "https://example.com/calendars/airbnb/{$unit->slug}.ics",
                'is_active' => true,
            ]);

            $unit->blockedDates()->delete();
            $unit->blockedDates()->createMany([
                [
                    'calendar_source_id' => $bookingCalendar->id,
                    'source' => 'Booking',
                    'starts_at' => now()->addDays(12)->toDateString(),
                    'ends_at' => now()->addDays(15)->toDateString(),
                    'summary' => 'Reserva fictícia',
                ],
                [
                    'calendar_source_id' => $airbnbCalendar->id,
                    'source' => 'Airbnb',
                    'starts_at' => now()->addDays(34)->toDateString(),
                    'ends_at' => now()->addDays(37)->toDateString(),
                    'summary' => 'Bloqueio de exemplo',
                ],
            ]);
        });
    }
}
