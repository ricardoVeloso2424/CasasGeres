<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActivityCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_activities(): void
    {
        $this->get('/admin/activities')->assertRedirect('/login');
    }

    public function test_admin_can_see_activities_index(): void
    {
        $this->seed();

        $this
            ->actingAsAdmin()
            ->get('/admin/activities')
            ->assertOk()
            ->assertSee('Gerir atividades')
            ->assertSee('Trilho da Preguiça');
    }

    public function test_admin_can_create_activity(): void
    {
        $this->seed();

        $response = $this
            ->actingAsAdmin()
            ->post(route('admin.activities.store'), [
                'title' => 'Restaurante da Serra',
                'slug' => '',
                'category' => 'Restaurantes',
                'description' => 'Restaurante familiar para jantar depois dos trilhos.',
                'location' => 'Gerês',
                'distance' => 'cerca de 15 min',
                'image' => 'https://example.com/restaurante.jpg',
                'is_featured' => '1',
                'is_active' => '1',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activities', [
            'title' => 'Restaurante da Serra',
            'slug' => 'restaurante-da-serra',
            'category' => 'Restaurantes',
            'is_featured' => true,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_edit_activity(): void
    {
        $this->seed();
        $activity = Activity::query()->where('slug', 'trilho-da-preguica')->firstOrFail();

        $response = $this
            ->actingAsAdmin()
            ->put(route('admin.activities.update', $activity), [
                'title' => 'Trilho da Preguiça Atualizado',
                'slug' => 'trilho-preguica-atualizado',
                'category' => 'Trilhos',
                'description' => 'Descricao atualizada.',
                'location' => 'Vilar da Veiga',
                'distance' => '20 min',
                'image' => 'https://example.com/trilho.jpg',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.activities.edit', 'trilho-preguica-atualizado'));

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'title' => 'Trilho da Preguiça Atualizado',
            'slug' => 'trilho-preguica-atualizado',
            'is_featured' => false,
        ]);
    }

    public function test_admin_cannot_create_activity_without_title(): void
    {
        $this->seed();

        $response = $this
            ->actingAsAdmin()
            ->post(route('admin.activities.store'), [
                'title' => '',
                'category' => 'Trilhos',
                'description' => 'Descricao.',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_admin_cannot_create_activity_without_description(): void
    {
        $this->seed();

        $response = $this
            ->actingAsAdmin()
            ->post(route('admin.activities.store'), [
                'title' => 'Atividade sem descricao',
                'category' => 'Trilhos',
                'description' => '',
            ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_inactive_activity_does_not_appear_on_public_activities_page(): void
    {
        $this->seed();

        Activity::query()->create([
            'title' => 'Atividade escondida',
            'slug' => 'atividade-escondida',
            'category' => 'Trilhos',
            'description' => 'Esta atividade nao deve aparecer publicamente.',
            'is_active' => false,
            'is_featured' => false,
        ]);

        $this
            ->get('/atividades')
            ->assertOk()
            ->assertDontSee('Atividade escondida');
    }

    public function test_active_activity_appears_on_public_activities_page(): void
    {
        $this->seed();

        Activity::query()->create([
            'title' => 'Atividade publica',
            'slug' => 'atividade-publica',
            'category' => 'Miradouros',
            'description' => 'Esta atividade deve aparecer publicamente.',
            'is_active' => true,
            'is_featured' => false,
        ]);

        $this
            ->get('/atividades')
            ->assertOk()
            ->assertSee('Atividade publica');
    }

    private function actingAsAdmin(): self
    {
        return $this->actingAs(User::query()->where('email', 'admin@example.com')->firstOrFail());
    }
}
