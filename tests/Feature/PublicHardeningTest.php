<?php

namespace Tests\Feature;

use App\Mail\NewBookingRequestReceived;
use App\Mail\NewContactMessageReceived;
use App\Models\House;
use App\Models\RentalUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_post_is_rate_limited(): void
    {
        $server = ['REMOTE_ADDR' => '203.0.113.10'];

        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this
                ->withServerVariables($server)
                ->post(route('login.store'), [
                    'email' => 'missing@example.com',
                    'password' => 'wrong-password',
                ])
                ->assertSessionHasErrors('email');
        }

        $this
            ->withServerVariables($server)
            ->post(route('login.store'), [
                'email' => 'missing@example.com',
                'password' => 'wrong-password',
            ])
            ->assertStatus(429);
    }

    public function test_contact_honeypot_rejects_spam_without_creating_message(): void
    {
        $this
            ->from(route('contact.index'))
            ->post(route('contact.store'), [
                'name' => 'Spam Contacto',
                'email' => 'spam@example.com',
                'phone' => null,
                'subject' => 'Spam',
                'message' => 'Mensagem automatica.',
                'website' => 'https://spam.example.com',
            ])
            ->assertRedirect(route('contact.index'))
            ->assertSessionHasErrors('contact');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_booking_honeypot_rejects_spam_without_creating_request(): void
    {
        $unit = $this->createRentalUnit();

        $this
            ->from(route('houses.units.show', [$unit->house, $unit]))
            ->post(route('booking-requests.store'), $this->validBookingPayload($unit, [
                'website' => 'https://spam.example.com',
            ]))
            ->assertRedirect(route('houses.units.show', [$unit->house, $unit]))
            ->assertSessionHasErrors('contact');

        $this->assertDatabaseCount('booking_requests', 0);
    }

    public function test_new_booking_request_sends_owner_email(): void
    {
        Mail::fake();
        config(['site.email' => 'owner@example.com']);
        $unit = $this->createRentalUnit();

        $this
            ->post(route('booking-requests.store'), $this->validBookingPayload($unit))
            ->assertSessionHasNoErrors();

        Mail::assertSent(NewBookingRequestReceived::class, function (NewBookingRequestReceived $mail): bool {
            return $mail->hasTo('owner@example.com')
                && $mail->bookingRequest->name === 'Cliente Teste'
                && $mail->bookingRequest->rentalUnit->name === 'T1 Teste';
        });
    }

    public function test_new_contact_message_sends_owner_email(): void
    {
        Mail::fake();
        config(['site.email' => 'owner@example.com']);

        $this
            ->post(route('contact.store'), [
                'name' => 'Cliente Contacto',
                'email' => 'contacto@example.com',
                'phone' => null,
                'subject' => 'Disponibilidade',
                'message' => 'Gostava de confirmar disponibilidade.',
            ])
            ->assertSessionHasNoErrors();

        Mail::assertSent(NewContactMessageReceived::class, function (NewContactMessageReceived $mail): bool {
            return $mail->hasTo('owner@example.com')
                && $mail->contactMessage->name === 'Cliente Contacto'
                && $mail->contactMessage->subject === 'Disponibilidade';
        });
    }

    public function test_empty_site_email_does_not_break_public_submissions(): void
    {
        Mail::fake();
        config(['site.email' => '']);
        $unit = $this->createRentalUnit();

        $this
            ->post(route('contact.store'), [
                'name' => 'Cliente Sem Email Site',
                'email' => 'contacto-sem-site@example.com',
                'phone' => null,
                'subject' => 'Contacto',
                'message' => 'Mensagem valida.',
            ])
            ->assertSessionHasNoErrors();

        $this
            ->post(route('booking-requests.store'), $this->validBookingPayload($unit, [
                'email' => 'reserva-sem-site@example.com',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'contacto-sem-site@example.com',
        ]);
        $this->assertDatabaseHas('booking_requests', [
            'email' => 'reserva-sem-site@example.com',
            'status' => 'pending',
        ]);
        Mail::assertNothingSent();
    }

    private function createRentalUnit(array $overrides = []): RentalUnit
    {
        $house = House::query()->create([
            'name' => 'Casa Teste',
            'slug' => 'casa-teste',
            'short_description' => 'Casa de teste.',
            'description' => 'Casa criada para testes de hardening.',
            'location' => 'Geres',
            'is_active' => true,
            'featured' => false,
        ]);

        return $house->rentalUnits()->create(array_merge([
            'name' => 'T1 Teste',
            'slug' => 't1-teste',
            'type' => 'T1',
            'short_description' => 'Unidade de teste.',
            'description' => 'Unidade criada para testes de hardening.',
            'capacity' => 2,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'base_price' => 80,
            'rules' => null,
            'is_active' => true,
            'featured' => false,
        ], $overrides));
    }

    private function validBookingPayload(RentalUnit $unit, array $overrides = []): array
    {
        return array_merge([
            'rental_unit_id' => $unit->id,
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'phone' => null,
            'check_in' => today()->addDays(20)->toDateString(),
            'check_out' => today()->addDays(23)->toDateString(),
            'guests' => 2,
            'message' => 'Gostava de pedir disponibilidade.',
        ], $overrides);
    }
}
