<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/houses')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_admin_dashboard(): void
    {
        $this->seed();
        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this
            ->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Acessos rapidos');
    }

    public function test_public_pages_remain_accessible_without_authentication(): void
    {
        $this->seed();

        foreach (['/', '/casas', '/casas/casa-do-rio', '/casas/casa-do-rio/t1-a', '/atividades', '/contactos', '/sobre', '/perguntas-frequentes'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_admin_dashboard_shows_main_counts(): void
    {
        $this->seed();

        ContactMessage::query()->create([
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
            'subject' => 'Pedido de contacto',
            'message' => 'Mensagem de teste.',
            'status' => 'unread',
        ]);

        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this
            ->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Casas')
            ->assertSee('3')
            ->assertSee('Unidades alugaveis')
            ->assertSee('6')
            ->assertSee('Pedidos pendentes')
            ->assertSee('1')
            ->assertSee('Mensagens nao lidas')
            ->assertSee('Atividades ativas')
            ->assertSee('Calendarios ativos')
            ->assertSee('Datas bloqueadas futuras')
            ->assertSee('Gerir iCal sources')
            ->assertSee('Gerir datas bloqueadas')
            ->assertSee('Proximo bloqueio futuro')
            ->assertSee('Reserva fict')
            ->assertSee('Ultimos pedidos de reserva')
            ->assertSee('Maria Silva')
            ->assertSee('Ultimas mensagens')
            ->assertSee('Cliente')
            ->assertSee('Pedido de contacto')
            ->assertSee('12');
    }
}
