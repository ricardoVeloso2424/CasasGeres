<?php

namespace Tests\Feature;

use App\Models\BlockedDate;
use App\Models\BookingRequest;
use App\Models\RentalUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_booking_requests(): void
    {
        $this->get('/admin/booking-requests')->assertRedirect('/login');
    }

    public function test_admin_can_see_booking_requests_index(): void
    {
        $this->seed();

        $this
            ->actingAsAdmin()
            ->get('/admin/booking-requests')
            ->assertOk()
            ->assertSee('Gerir pedidos de reserva')
            ->assertSee('Maria Silva')
            ->assertSee('Casa do Rio')
            ->assertSee('T1 A');
    }

    public function test_admin_can_see_booking_request_detail(): void
    {
        $this->seed();
        $bookingRequest = BookingRequest::query()->where('name', 'Maria Silva')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->get(route('admin.booking-requests.show', $bookingRequest))
            ->assertOk()
            ->assertSee('Detalhe do pedido')
            ->assertSee('Maria Silva')
            ->assertSee('Gostava de confirmar disponibilidade');
    }

    public function test_admin_can_update_booking_request_status_to_contacted(): void
    {
        $this->seed();
        $bookingRequest = BookingRequest::query()->where('status', 'pending')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->patch(route('admin.booking-requests.update-status', $bookingRequest), [
                'status' => 'contacted',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('booking_requests', [
            'id' => $bookingRequest->id,
            'status' => 'contacted',
        ]);
    }

    public function test_admin_can_update_booking_request_status_to_confirmed(): void
    {
        $this->seed();
        $bookingRequest = BookingRequest::query()->where('status', 'pending')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->patch(route('admin.booking-requests.update-status', $bookingRequest), [
                'status' => 'confirmed',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('booking_requests', [
            'id' => $bookingRequest->id,
            'status' => 'confirmed',
        ]);
        $this->assertDatabaseHas('blocked_dates', [
            'rental_unit_id' => $bookingRequest->rental_unit_id,
            'source' => 'Direct',
            'external_uid' => "direct-booking-request-{$bookingRequest->id}",
            'summary' => "Reserva direta confirmada: {$bookingRequest->name}",
        ]);
    }

    public function test_confirming_booking_request_does_not_duplicate_blocked_date(): void
    {
        $this->seed();
        $bookingRequest = BookingRequest::query()->where('status', 'pending')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->patch(route('admin.booking-requests.update-status', $bookingRequest), [
                'status' => 'confirmed',
            ])
            ->assertRedirect();

        $this
            ->actingAsAdmin()
            ->patch(route('admin.booking-requests.update-status', $bookingRequest), [
                'status' => 'confirmed',
            ])
            ->assertRedirect();

        $this->assertSame(1, BlockedDate::query()
            ->where('source', 'Direct')
            ->where('external_uid', "direct-booking-request-{$bookingRequest->id}")
            ->count());
    }

    public function test_confirming_booking_request_fails_when_dates_are_blocked(): void
    {
        $this->seed();
        $bookingRequest = BookingRequest::query()->where('status', 'pending')->firstOrFail();

        $bookingRequest->rentalUnit->blockedDates()->create([
            'source' => 'Manual',
            'starts_at' => $bookingRequest->check_in->copy()->addDay()->toDateString(),
            'ends_at' => $bookingRequest->check_out->toDateString(),
            'summary' => 'Bloqueio manual sobreposto',
        ]);

        $this
            ->actingAsAdmin()
            ->patch(route('admin.booking-requests.update-status', $bookingRequest), [
                'status' => 'confirmed',
            ])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('booking_requests', [
            'id' => $bookingRequest->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('blocked_dates', [
            'source' => 'Direct',
            'external_uid' => "direct-booking-request-{$bookingRequest->id}",
        ]);
    }

    public function test_confirmed_booking_request_blocks_public_booking_for_same_dates(): void
    {
        $this->seed();
        $bookingRequest = BookingRequest::query()->where('status', 'pending')->firstOrFail();
        $unit = $bookingRequest->rentalUnit;

        $this
            ->actingAsAdmin()
            ->patch(route('admin.booking-requests.update-status', $bookingRequest), [
                'status' => 'confirmed',
            ])
            ->assertRedirect();

        $this
            ->from(route('houses.units.show', [$unit->house, $unit]))
            ->post(route('booking-requests.store'), $this->validPublicPayload($unit, [
                'check_in' => $bookingRequest->check_in->toDateString(),
                'check_out' => $bookingRequest->check_out->toDateString(),
            ]))
            ->assertRedirect(route('houses.units.show', [$unit->house, $unit]))
            ->assertSessionHasErrors('check_in');

        $this->assertDatabaseMissing('booking_requests', [
            'email' => 'public-after-confirm@example.com',
        ]);
    }

    public function test_admin_cannot_update_booking_request_to_invalid_status(): void
    {
        $this->seed();
        $bookingRequest = BookingRequest::query()->where('status', 'pending')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->patch(route('admin.booking-requests.update-status', $bookingRequest), [
                'status' => 'invalid',
            ])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('booking_requests', [
            'id' => $bookingRequest->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_delete_booking_request(): void
    {
        $this->seed();
        $bookingRequest = BookingRequest::query()->where('name', 'Maria Silva')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->delete(route('admin.booking-requests.destroy', $bookingRequest))
            ->assertRedirect(route('admin.booking-requests.index'));

        $this->assertDatabaseMissing('booking_requests', [
            'id' => $bookingRequest->id,
        ]);
    }

    private function actingAsAdmin(): self
    {
        return $this->actingAs(User::query()->where('email', 'admin@example.com')->firstOrFail());
    }

    private function validPublicPayload(RentalUnit $unit, array $overrides = []): array
    {
        return array_merge([
            'rental_unit_id' => $unit->id,
            'name' => 'Cliente Publico',
            'email' => 'public-after-confirm@example.com',
            'phone' => null,
            'check_in' => today()->addDays(120)->toDateString(),
            'check_out' => today()->addDays(123)->toDateString(),
            'guests' => 2,
            'message' => 'Pedido publico depois da confirmacao.',
        ], $overrides);
    }
}
