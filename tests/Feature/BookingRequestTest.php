<?php

namespace Tests\Feature;

use App\Models\House;
use App\Models\RentalUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_booking_request_with_available_dates(): void
    {
        $unit = $this->createRentalUnit();

        $response = $this
            ->from(route('houses.units.show', [$unit->house, $unit]))
            ->post(route('booking-requests.store'), $this->validPayload($unit));

        $response
            ->assertRedirect(route('houses.units.show', [$unit->house, $unit]))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('booking_status');

        $this->assertDatabaseHas('booking_requests', [
            'rental_unit_id' => $unit->id,
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_rejects_booking_request_with_overlapping_blocked_dates(): void
    {
        $unit = $this->createRentalUnit();
        $unit->blockedDates()->create([
            'source' => 'Booking',
            'starts_at' => today()->addDays(10)->toDateString(),
            'ends_at' => today()->addDays(15)->toDateString(),
            'summary' => 'Reserva existente',
        ]);

        $response = $this->post(route('booking-requests.store'), $this->validPayload($unit, [
            'check_in' => today()->addDays(14)->toDateString(),
            'check_out' => today()->addDays(16)->toDateString(),
        ]));

        $response->assertSessionHasErrors('check_in');
        $this->assertDatabaseCount('booking_requests', 0);
    }

    public function test_rejects_booking_request_with_overlapping_manual_blocked_date(): void
    {
        $unit = $this->createRentalUnit();
        $unit->blockedDates()->create([
            'source' => 'Manual',
            'starts_at' => today()->addDays(20)->toDateString(),
            'ends_at' => today()->addDays(23)->toDateString(),
            'summary' => 'Bloqueio manual criado no admin.',
        ]);

        $response = $this->post(route('booking-requests.store'), $this->validPayload($unit, [
            'check_in' => today()->addDays(21)->toDateString(),
            'check_out' => today()->addDays(22)->toDateString(),
        ]));

        $response->assertSessionHasErrors('check_in');
        $this->assertDatabaseCount('booking_requests', 0);
    }

    public function test_rejects_booking_request_with_overlapping_imported_blocked_date(): void
    {
        $unit = $this->createRentalUnit();
        $calendarSource = $unit->calendarSources()->create([
            'platform' => 'Booking',
            'ical_url' => 'https://calendar.test/imported.ics',
            'is_active' => true,
        ]);
        $unit->blockedDates()->create([
            'calendar_source_id' => $calendarSource->id,
            'source' => 'Booking',
            'external_uid' => 'imported@example.com',
            'starts_at' => today()->addDays(20)->toDateString(),
            'ends_at' => today()->addDays(23)->toDateString(),
            'summary' => 'Bloqueio importado.',
        ]);

        $response = $this->post(route('booking-requests.store'), $this->validPayload($unit, [
            'check_in' => today()->addDays(21)->toDateString(),
            'check_out' => today()->addDays(22)->toDateString(),
        ]));

        $response->assertSessionHasErrors('check_in');
        $this->assertDatabaseCount('booking_requests', 0);
    }

    public function test_allows_booking_request_when_check_in_matches_blocked_date_end(): void
    {
        $unit = $this->createRentalUnit();
        $unit->blockedDates()->create([
            'source' => 'Booking',
            'starts_at' => today()->addDays(10)->toDateString(),
            'ends_at' => today()->addDays(15)->toDateString(),
            'summary' => 'Reserva existente',
        ]);

        $response = $this->post(route('booking-requests.store'), $this->validPayload($unit, [
            'check_in' => today()->addDays(15)->toDateString(),
            'check_out' => today()->addDays(18)->toDateString(),
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('booking_requests', [
            'rental_unit_id' => $unit->id,
            'status' => 'pending',
        ]);
    }

    public function test_rejects_booking_request_when_check_out_is_not_after_check_in(): void
    {
        $unit = $this->createRentalUnit();

        $response = $this->post(route('booking-requests.store'), $this->validPayload($unit, [
            'check_in' => today()->addDays(20)->toDateString(),
            'check_out' => today()->addDays(20)->toDateString(),
        ]));

        $response->assertSessionHasErrors('check_out');
        $this->assertDatabaseCount('booking_requests', 0);
    }

    public function test_rejects_booking_request_without_email_or_phone(): void
    {
        $unit = $this->createRentalUnit();

        $response = $this->post(route('booking-requests.store'), $this->validPayload($unit, [
            'email' => null,
            'phone' => null,
        ]));

        $response->assertSessionHasErrors('contact');
        $this->assertDatabaseCount('booking_requests', 0);
    }

    public function test_rejects_booking_request_when_guests_exceed_unit_capacity(): void
    {
        $unit = $this->createRentalUnit(['capacity' => 2]);

        $response = $this->post(route('booking-requests.store'), $this->validPayload($unit, [
            'guests' => 3,
        ]));

        $response->assertSessionHasErrors('guests');
        $this->assertDatabaseCount('booking_requests', 0);
    }

    public function test_rejects_booking_request_for_inactive_unit(): void
    {
        $unit = $this->createRentalUnit(['is_active' => false]);

        $response = $this->post(route('booking-requests.store'), $this->validPayload($unit));

        $response->assertSessionHasErrors('rental_unit_id');
        $this->assertDatabaseCount('booking_requests', 0);
    }

    private function createRentalUnit(array $overrides = []): RentalUnit
    {
        $house = House::query()->create([
            'name' => 'Casa Teste',
            'slug' => 'casa-teste',
            'short_description' => 'Casa de teste.',
            'description' => 'Casa criada para testes de pedidos de reserva.',
            'location' => 'Gerês',
            'is_active' => true,
            'featured' => false,
        ]);

        return $house->rentalUnits()->create(array_merge([
            'name' => 'T1 Teste',
            'slug' => 't1-teste',
            'type' => 'T1',
            'short_description' => 'Unidade de teste.',
            'description' => 'Unidade criada para testar disponibilidade.',
            'capacity' => 2,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'base_price' => 80,
            'rules' => null,
            'is_active' => true,
            'featured' => false,
        ], $overrides));
    }

    private function validPayload(RentalUnit $unit, array $overrides = []): array
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
