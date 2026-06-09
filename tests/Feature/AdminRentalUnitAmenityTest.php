<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\House;
use App\Models\RentalUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRentalUnitAmenityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_associate_amenities_when_creating_rental_unit(): void
    {
        $this->seed();

        $house = House::query()->where('slug', 'casa-do-rio')->firstOrFail();
        $amenities = Amenity::query()->whereIn('slug', ['wi-fi', 'estacionamento'])->pluck('id')->all();

        $this
            ->actingAsAdmin()
            ->post(route('admin.rental-units.store'), [
                'house_id' => $house->id,
                'name' => 'T0 Comodidades',
                'type' => 'T0',
                'short_description' => 'Unidade com comodidades.',
                'description' => 'Descricao.',
                'capacity' => 2,
                'bedrooms' => 0,
                'bathrooms' => 1,
                'amenity_ids' => $amenities,
                'is_active' => '1',
            ])
            ->assertRedirect();

        $unit = RentalUnit::query()->where('slug', 't0-comodidades')->firstOrFail();

        foreach ($amenities as $amenityId) {
            $this->assertDatabaseHas('amenity_rental_unit', [
                'amenity_id' => $amenityId,
                'rental_unit_id' => $unit->id,
            ]);
        }
    }

    public function test_admin_can_update_rental_unit_amenities(): void
    {
        $this->seed();

        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $amenity = Amenity::query()->where('slug', 'churrasqueira')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->put(route('admin.rental-units.update', $unit->id), [
                'house_id' => $unit->house_id,
                'name' => $unit->name,
                'slug' => $unit->slug,
                'type' => $unit->type,
                'short_description' => $unit->short_description,
                'description' => $unit->description,
                'capacity' => $unit->capacity,
                'bedrooms' => $unit->bedrooms,
                'bathrooms' => $unit->bathrooms,
                'base_price' => $unit->base_price,
                'rules' => $unit->rules,
                'amenity_ids' => [$amenity->id],
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.rental-units.edit', $unit->id));

        $this->assertDatabaseHas('amenity_rental_unit', [
            'amenity_id' => $amenity->id,
            'rental_unit_id' => $unit->id,
        ]);

        $this->assertSame(1, $unit->fresh()->amenities()->count());
    }

    public function test_public_unit_page_shows_associated_amenities(): void
    {
        $this->seed();

        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $amenity = Amenity::query()->create([
            'name' => 'Sauna privada',
            'slug' => 'sauna-privada',
            'icon' => 'sauna',
        ]);

        $unit->amenities()->sync([$amenity->id]);

        $this
            ->get(route('houses.units.show', [$unit->house, $unit]))
            ->assertOk()
            ->assertSee('Comodidades')
            ->assertSee('Sauna privada');
    }

    private function actingAsAdmin(): self
    {
        return $this->actingAs(User::query()->where('email', 'admin@example.com')->firstOrFail());
    }
}
