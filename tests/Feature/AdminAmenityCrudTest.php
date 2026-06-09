<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAmenityCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_amenities(): void
    {
        $this->get('/admin/amenities')->assertRedirect('/login');
    }

    public function test_admin_can_see_amenities_index(): void
    {
        $this->seed();

        $this
            ->actingAsAdmin()
            ->get('/admin/amenities')
            ->assertOk()
            ->assertSee('Gerir comodidades')
            ->assertSee('Wi-Fi');
    }

    public function test_admin_can_create_amenity(): void
    {
        $this->seed();

        $response = $this
            ->actingAsAdmin()
            ->post(route('admin.amenities.store'), [
                'name' => 'Lareira',
                'slug' => '',
                'icon' => 'fireplace',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('amenities', [
            'name' => 'Lareira',
            'slug' => 'lareira',
            'icon' => 'fireplace',
        ]);
    }

    public function test_admin_can_edit_amenity(): void
    {
        $this->seed();
        $amenity = Amenity::query()->where('slug', 'wi-fi')->firstOrFail();

        $response = $this
            ->actingAsAdmin()
            ->put(route('admin.amenities.update', $amenity), [
                'name' => 'Wi-Fi rapido',
                'slug' => 'wifi-rapido',
                'icon' => 'wifi',
            ]);

        $response->assertRedirect(route('admin.amenities.edit', 'wifi-rapido'));

        $this->assertDatabaseHas('amenities', [
            'id' => $amenity->id,
            'name' => 'Wi-Fi rapido',
            'slug' => 'wifi-rapido',
        ]);
    }

    public function test_admin_cannot_create_amenity_without_name(): void
    {
        $this->seed();

        $response = $this
            ->actingAsAdmin()
            ->post(route('admin.amenities.store'), [
                'name' => '',
                'icon' => 'wifi',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_cannot_delete_amenity_associated_to_unit(): void
    {
        $this->seed();
        $amenity = Amenity::query()->where('slug', 'wi-fi')->firstOrFail();

        $response = $this
            ->actingAsAdmin()
            ->delete(route('admin.amenities.destroy', $amenity));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('amenities', ['id' => $amenity->id]);
    }

    private function actingAsAdmin(): self
    {
        return $this->actingAs(User::query()->where('email', 'admin@example.com')->firstOrFail());
    }
}
