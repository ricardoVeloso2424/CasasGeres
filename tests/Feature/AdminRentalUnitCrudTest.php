<?php

namespace Tests\Feature;

use App\Models\House;
use App\Models\RentalUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRentalUnitCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_rental_units(): void
    {
        $this->get('/admin/rental-units')->assertRedirect('/login');
    }

    public function test_admin_can_see_rental_units_index(): void
    {
        $this->seed();

        $this
            ->actingAsAdmin()
            ->get('/admin/rental-units')
            ->assertOk()
            ->assertSee('Gerir unidades')
            ->assertSee('T1 A');
    }

    public function test_admin_can_create_rental_unit_for_house(): void
    {
        $this->seed();
        $house = House::query()->where('slug', 'casa-do-rio')->firstOrFail();

        $response = $this
            ->actingAsAdmin()
            ->post(route('admin.rental-units.store'), [
                'house_id' => $house->id,
                'name' => 'T2 Novo',
                'slug' => '',
                'type' => 'T2',
                'short_description' => 'Unidade nova.',
                'description' => 'Descricao da unidade nova.',
                'capacity' => 4,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'base_price' => 120,
                'rules' => 'Sem regras especiais.',
                'is_active' => '1',
            ]);

        $unit = RentalUnit::query()->where('slug', 't2-novo')->firstOrFail();

        $response->assertRedirect(route('admin.rental-units.edit', $unit->id));
        $this->assertDatabaseHas('rental_units', [
            'house_id' => $house->id,
            'name' => 'T2 Novo',
            'slug' => 't2-novo',
            'capacity' => 4,
            'featured' => false,
        ]);
    }

    public function test_admin_can_edit_rental_unit(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();

        $response = $this
            ->actingAsAdmin()
            ->put(route('admin.rental-units.update', $unit->id), [
                'house_id' => $unit->house_id,
                'name' => 'T1 A Renovado',
                'slug' => 't1-a-renovado',
                'type' => 'T1',
                'short_description' => 'Descricao renovada.',
                'description' => 'Descricao completa renovada.',
                'capacity' => 2,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'base_price' => 95,
                'rules' => null,
                'is_active' => '1',
                'featured' => '1',
            ]);

        $response->assertRedirect(route('admin.rental-units.edit', $unit->id));

        $this->assertDatabaseHas('rental_units', [
            'id' => $unit->id,
            'name' => 'T1 A Renovado',
            'slug' => 't1-a-renovado',
            'base_price' => 95,
            'featured' => true,
        ]);
    }

    public function test_admin_cannot_create_rental_unit_without_house_id(): void
    {
        $this->seed();

        $response = $this
            ->actingAsAdmin()
            ->post(route('admin.rental-units.store'), [
                'name' => 'Unidade sem casa',
                'type' => 'T1',
                'capacity' => 2,
            ]);

        $response->assertSessionHasErrors('house_id');
        $this->assertDatabaseMissing('rental_units', ['name' => 'Unidade sem casa']);
    }

    public function test_admin_cannot_create_rental_unit_with_capacity_lower_than_one(): void
    {
        $this->seed();
        $house = House::query()->where('slug', 'casa-do-rio')->firstOrFail();

        $response = $this
            ->actingAsAdmin()
            ->post(route('admin.rental-units.store'), [
                'house_id' => $house->id,
                'name' => 'Unidade invalida',
                'type' => 'T1',
                'capacity' => 0,
            ]);

        $response->assertSessionHasErrors('capacity');
        $this->assertDatabaseMissing('rental_units', ['name' => 'Unidade invalida']);
    }

    public function test_admin_cannot_delete_rental_unit_with_blocked_dates_or_booking_requests(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();

        $response = $this
            ->actingAsAdmin()
            ->delete(route('admin.rental-units.destroy', $unit->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('rental_units', ['id' => $unit->id]);
    }

    private function actingAsAdmin(): self
    {
        return $this->actingAs(User::query()->where('email', 'admin@example.com')->firstOrFail());
    }
}
