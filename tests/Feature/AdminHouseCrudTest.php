<?php

namespace Tests\Feature;

use App\Models\House;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminHouseCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_houses(): void
    {
        $this->get('/admin/houses')->assertRedirect('/login');
    }

    public function test_admin_can_see_houses_index(): void
    {
        $this->seed();

        $this
            ->actingAsAdmin()
            ->get('/admin/houses')
            ->assertOk()
            ->assertSee('Gerir casas')
            ->assertSee('Casa do Rio');
    }

    public function test_admin_can_create_house(): void
    {
        $this->seed();

        $response = $this
            ->actingAsAdmin()
            ->post(route('admin.houses.store'), [
                'name' => 'Casa Nova',
                'slug' => '',
                'short_description' => 'Descricao curta.',
                'description' => 'Descricao completa.',
                'location' => 'Geres',
                'address_optional' => 'Rua de teste',
                'latitude' => '41.7001',
                'longitude' => '-8.2001',
                'is_active' => '1',
                'featured' => '1',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('houses', [
            'name' => 'Casa Nova',
            'slug' => 'casa-nova',
            'is_active' => true,
            'featured' => true,
        ]);
    }

    public function test_admin_can_edit_house(): void
    {
        $this->seed();
        $house = House::query()->where('slug', 'casa-do-rio')->firstOrFail();

        $response = $this
            ->actingAsAdmin()
            ->put(route('admin.houses.update', $house), [
                'name' => 'Casa do Rio Atualizada',
                'slug' => 'casa-do-rio-atualizada',
                'short_description' => 'Nova descricao curta.',
                'description' => 'Nova descricao completa.',
                'location' => 'Nova zona',
                'address_optional' => null,
                'latitude' => null,
                'longitude' => null,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.houses.edit', 'casa-do-rio-atualizada'));

        $this->assertDatabaseHas('houses', [
            'id' => $house->id,
            'name' => 'Casa do Rio Atualizada',
            'slug' => 'casa-do-rio-atualizada',
            'featured' => false,
        ]);
    }

    public function test_admin_cannot_create_house_without_name(): void
    {
        $this->seed();

        $response = $this
            ->actingAsAdmin()
            ->post(route('admin.houses.store'), [
                'name' => '',
                'location' => 'Geres',
            ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('houses', ['slug' => '']);
    }

    public function test_admin_cannot_delete_house_with_rental_units(): void
    {
        $this->seed();
        $house = House::query()->where('slug', 'casa-do-rio')->firstOrFail();

        $response = $this
            ->actingAsAdmin()
            ->delete(route('admin.houses.destroy', $house));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('houses', ['id' => $house->id]);
    }

    private function actingAsAdmin(): self
    {
        return $this->actingAs(User::query()->where('email', 'admin@example.com')->firstOrFail());
    }
}
