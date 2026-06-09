<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_contact_messages(): void
    {
        $this->get('/admin/contact-messages')->assertRedirect('/login');
    }

    public function test_admin_can_see_contact_messages_index(): void
    {
        $this->seed();
        $contactMessage = $this->createContactMessage();

        $this
            ->actingAsAdmin()
            ->get('/admin/contact-messages')
            ->assertOk()
            ->assertSee('Gerir mensagens')
            ->assertSee($contactMessage->name)
            ->assertSee($contactMessage->subject);
    }

    public function test_admin_can_see_contact_message_detail(): void
    {
        $this->seed();
        $contactMessage = $this->createContactMessage();

        $this
            ->actingAsAdmin()
            ->get(route('admin.contact-messages.show', $contactMessage))
            ->assertOk()
            ->assertSee('Detalhe da mensagem')
            ->assertSee($contactMessage->name)
            ->assertSee($contactMessage->message);
    }

    public function test_admin_can_update_contact_message_status_to_read(): void
    {
        $this->seed();
        $contactMessage = $this->createContactMessage();

        $this
            ->actingAsAdmin()
            ->patch(route('admin.contact-messages.update-status', $contactMessage), [
                'status' => 'read',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contact_messages', [
            'id' => $contactMessage->id,
            'status' => 'read',
        ]);
    }

    public function test_admin_can_update_contact_message_status_to_archived(): void
    {
        $this->seed();
        $contactMessage = $this->createContactMessage();

        $this
            ->actingAsAdmin()
            ->patch(route('admin.contact-messages.update-status', $contactMessage), [
                'status' => 'archived',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contact_messages', [
            'id' => $contactMessage->id,
            'status' => 'archived',
        ]);
    }

    public function test_admin_cannot_update_contact_message_to_invalid_status(): void
    {
        $this->seed();
        $contactMessage = $this->createContactMessage();

        $this
            ->actingAsAdmin()
            ->patch(route('admin.contact-messages.update-status', $contactMessage), [
                'status' => 'invalid',
            ])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('contact_messages', [
            'id' => $contactMessage->id,
            'status' => 'unread',
        ]);
    }

    public function test_admin_can_delete_contact_message(): void
    {
        $this->seed();
        $contactMessage = $this->createContactMessage();

        $this
            ->actingAsAdmin()
            ->delete(route('admin.contact-messages.destroy', $contactMessage))
            ->assertRedirect(route('admin.contact-messages.index'));

        $this->assertDatabaseMissing('contact_messages', [
            'id' => $contactMessage->id,
        ]);
    }

    private function createContactMessage(array $overrides = []): ContactMessage
    {
        return ContactMessage::query()->create(array_merge([
            'name' => 'Cliente Contacto',
            'email' => 'contacto@example.com',
            'phone' => '+351 930 000 000',
            'subject' => 'Disponibilidade',
            'message' => 'Gostava de saber mais sobre disponibilidade.',
            'status' => 'unread',
        ], $overrides));
    }

    private function actingAsAdmin(): self
    {
        return $this->actingAs(User::query()->where('email', 'admin@example.com')->firstOrFail());
    }
}
