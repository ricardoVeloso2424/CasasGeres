<?php

namespace Tests\Feature;

use App\Models\BlockedDate;
use App\Models\CalendarSource;
use App\Models\RentalUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBlockedDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_blocked_dates(): void
    {
        $this->get('/admin/blocked-dates')->assertRedirect('/login');
    }

    public function test_admin_can_see_blocked_dates_index(): void
    {
        $this->seed();

        $this
            ->actingAsAdmin()
            ->get('/admin/blocked-dates')
            ->assertOk()
            ->assertSee('Gerir datas bloqueadas')
            ->assertSee('Reserva fict')
            ->assertSee('Casa do Rio')
            ->assertSee('T1 A');
    }

    public function test_admin_can_create_manual_blocked_date(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->post(route('admin.blocked-dates.store'), $this->validPayload($unit, [
                'starts_at' => today()->addDays(80)->toDateString(),
                'ends_at' => today()->addDays(83)->toDateString(),
                'summary' => 'Bloqueio manual teste',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('blocked_dates', [
            'rental_unit_id' => $unit->id,
            'source' => 'Manual',
            'starts_at' => today()->addDays(80)->startOfDay()->toDateTimeString(),
            'ends_at' => today()->addDays(83)->startOfDay()->toDateTimeString(),
            'summary' => 'Bloqueio manual teste',
        ]);
    }

    public function test_admin_can_edit_blocked_date(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $blockedDate = $this->createBlockedDate($unit, 80, 83);

        $this
            ->actingAsAdmin()
            ->put(route('admin.blocked-dates.update', $blockedDate), $this->validPayload($unit, [
                'starts_at' => today()->addDays(84)->toDateString(),
                'ends_at' => today()->addDays(86)->toDateString(),
                'source' => 'Outro',
                'summary' => 'Bloqueio editado',
            ]))
            ->assertRedirect(route('admin.blocked-dates.edit', $blockedDate));

        $this->assertDatabaseHas('blocked_dates', [
            'id' => $blockedDate->id,
            'source' => 'Outro',
            'starts_at' => today()->addDays(84)->startOfDay()->toDateTimeString(),
            'ends_at' => today()->addDays(86)->startOfDay()->toDateTimeString(),
            'summary' => 'Bloqueio editado',
        ]);
    }

    public function test_admin_cannot_create_blocked_date_with_end_not_after_start(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->post(route('admin.blocked-dates.store'), $this->validPayload($unit, [
                'starts_at' => today()->addDays(80)->toDateString(),
                'ends_at' => today()->addDays(80)->toDateString(),
            ]))
            ->assertSessionHasErrors('ends_at');
    }

    public function test_admin_cannot_create_overlapping_blocked_date_on_same_unit(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $this->createBlockedDate($unit, 80, 85);

        $this
            ->actingAsAdmin()
            ->post(route('admin.blocked-dates.store'), $this->validPayload($unit, [
                'starts_at' => today()->addDays(84)->toDateString(),
                'ends_at' => today()->addDays(88)->toDateString(),
                'summary' => 'Sobreposto',
            ]))
            ->assertSessionHasErrors('starts_at');

        $this->assertDatabaseMissing('blocked_dates', [
            'summary' => 'Sobreposto',
        ]);
    }

    public function test_admin_can_create_blocked_date_in_same_interval_on_another_unit(): void
    {
        $this->seed();
        $unitA = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $unitB = RentalUnit::query()->where('slug', 't1-b')->firstOrFail();
        $this->createBlockedDate($unitA, 80, 85);

        $this
            ->actingAsAdmin()
            ->post(route('admin.blocked-dates.store'), $this->validPayload($unitB, [
                'starts_at' => today()->addDays(80)->toDateString(),
                'ends_at' => today()->addDays(85)->toDateString(),
                'summary' => 'Mesmo intervalo noutra unidade',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('blocked_dates', [
            'rental_unit_id' => $unitB->id,
            'summary' => 'Mesmo intervalo noutra unidade',
        ]);
    }

    public function test_admin_can_create_blocked_date_starting_at_existing_end(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $this->createBlockedDate($unit, 80, 85);

        $this
            ->actingAsAdmin()
            ->post(route('admin.blocked-dates.store'), $this->validPayload($unit, [
                'starts_at' => today()->addDays(85)->toDateString(),
                'ends_at' => today()->addDays(88)->toDateString(),
                'summary' => 'Adjacente depois',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('blocked_dates', [
            'rental_unit_id' => $unit->id,
            'summary' => 'Adjacente depois',
        ]);
    }

    public function test_admin_can_create_blocked_date_ending_at_existing_start(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $this->createBlockedDate($unit, 80, 85);

        $this
            ->actingAsAdmin()
            ->post(route('admin.blocked-dates.store'), $this->validPayload($unit, [
                'starts_at' => today()->addDays(77)->toDateString(),
                'ends_at' => today()->addDays(80)->toDateString(),
                'summary' => 'Adjacente antes',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('blocked_dates', [
            'rental_unit_id' => $unit->id,
            'summary' => 'Adjacente antes',
        ]);
    }

    public function test_admin_cannot_associate_calendar_source_from_another_unit(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $otherUnit = RentalUnit::query()->where('slug', 't1-b')->firstOrFail();
        $otherCalendarSource = CalendarSource::query()->where('rental_unit_id', $otherUnit->id)->firstOrFail();

        $this
            ->actingAsAdmin()
            ->post(route('admin.blocked-dates.store'), $this->validPayload($unit, [
                'calendar_source_id' => $otherCalendarSource->id,
                'starts_at' => today()->addDays(90)->toDateString(),
                'ends_at' => today()->addDays(92)->toDateString(),
                'summary' => 'Fonte errada',
            ]))
            ->assertSessionHasErrors('calendar_source_id');

        $this->assertDatabaseMissing('blocked_dates', [
            'summary' => 'Fonte errada',
        ]);
    }

    public function test_admin_can_delete_blocked_date(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $blockedDate = $this->createBlockedDate($unit, 80, 83);

        $this
            ->actingAsAdmin()
            ->delete(route('admin.blocked-dates.destroy', $blockedDate))
            ->assertRedirect(route('admin.blocked-dates.index'));

        $this->assertDatabaseMissing('blocked_dates', [
            'id' => $blockedDate->id,
        ]);
    }

    public function test_public_unit_page_shows_new_blocked_date(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $this->createBlockedDate($unit, 90, 92, [
            'source' => 'Manual',
            'summary' => 'Manutencao teste',
        ]);

        $this
            ->get(route('houses.units.show', [$unit->house, $unit]))
            ->assertOk()
            ->assertSee(today()->addDays(90)->format('d/m/Y'))
            ->assertSee('Manual');
    }

    private function createBlockedDate(RentalUnit $unit, int $startsInDays, int $endsInDays, array $overrides = []): BlockedDate
    {
        return $unit->blockedDates()->create(array_merge([
            'source' => 'Manual',
            'starts_at' => today()->addDays($startsInDays)->toDateString(),
            'ends_at' => today()->addDays($endsInDays)->toDateString(),
            'summary' => 'Bloqueio existente',
        ], $overrides));
    }

    private function validPayload(RentalUnit $unit, array $overrides = []): array
    {
        return array_merge([
            'rental_unit_id' => $unit->id,
            'calendar_source_id' => null,
            'source' => 'Manual',
            'external_uid' => null,
            'starts_at' => today()->addDays(80)->toDateString(),
            'ends_at' => today()->addDays(83)->toDateString(),
            'summary' => 'Bloqueio manual',
        ], $overrides);
    }

    private function actingAsAdmin(): self
    {
        return $this->actingAs(User::query()->where('email', 'admin@example.com')->firstOrFail());
    }
}
