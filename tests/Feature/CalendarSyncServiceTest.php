<?php

namespace Tests\Feature;

use App\Models\BlockedDate;
use App\Models\CalendarSource;
use App\Models\House;
use App\Models\RentalUnit;
use App\Services\CalendarSyncService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CalendarSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_imports_simple_ical_event_with_uid(): void
    {
        $source = $this->createCalendarSource();
        Http::fake([$source->ical_url => Http::response($this->ics([
            $this->event('test-booking-1@example.com', '20260710', '20260715', 'Booking reservation'),
        ]))]);

        $result = app(CalendarSyncService::class)->sync($source);

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['created']);
        $this->assertSame('success', $source->fresh()->last_sync_status);
        $this->assertNull($source->fresh()->last_sync_error);
        $this->assertDatabaseHas('blocked_dates', [
            'calendar_source_id' => $source->id,
            'rental_unit_id' => $source->rental_unit_id,
            'source' => 'Booking',
            'external_uid' => 'test-booking-1@example.com',
            'starts_at' => '2026-07-10 00:00:00',
            'ends_at' => '2026-07-15 00:00:00',
            'summary' => 'Booking reservation',
        ]);
        $this->assertNotNull($source->fresh()->last_synced_at);
    }

    public function test_updates_existing_blocked_date_when_uid_exists(): void
    {
        $source = $this->createCalendarSource();
        $source->blockedDates()->create([
            'rental_unit_id' => $source->rental_unit_id,
            'source' => 'Booking',
            'external_uid' => 'same-uid@example.com',
            'starts_at' => '2026-07-01',
            'ends_at' => '2026-07-03',
            'summary' => 'Old summary',
        ]);
        Http::fake([$source->ical_url => Http::response($this->ics([
            $this->event('same-uid@example.com', '20260710', '20260715', 'Updated summary'),
        ]))]);

        $result = app(CalendarSyncService::class)->sync($source);

        $this->assertTrue($result['success']);
        $this->assertSame(0, $result['created']);
        $this->assertSame(1, $result['updated']);
        $this->assertDatabaseHas('blocked_dates', [
            'calendar_source_id' => $source->id,
            'external_uid' => 'same-uid@example.com',
            'starts_at' => '2026-07-10 00:00:00',
            'ends_at' => '2026-07-15 00:00:00',
            'summary' => 'Updated summary',
        ]);
    }

    public function test_removes_old_blocked_date_from_same_calendar_source(): void
    {
        $source = $this->createCalendarSource();
        $old = $source->blockedDates()->create([
            'rental_unit_id' => $source->rental_unit_id,
            'source' => 'Booking',
            'external_uid' => 'old@example.com',
            'starts_at' => '2026-07-01',
            'ends_at' => '2026-07-03',
            'summary' => 'Old reservation',
        ]);
        Http::fake([$source->ical_url => Http::response($this->ics([
            $this->event('new@example.com', '20260710', '20260715', 'New reservation'),
        ]))]);

        $result = app(CalendarSyncService::class)->sync($source);

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['deleted']);
        $this->assertDatabaseMissing('blocked_dates', ['id' => $old->id]);
    }

    public function test_does_not_remove_manual_blocked_date(): void
    {
        $source = $this->createCalendarSource();
        $manual = $source->rentalUnit->blockedDates()->create([
            'source' => 'Manual',
            'starts_at' => '2026-07-01',
            'ends_at' => '2026-07-03',
            'summary' => 'Manual block',
        ]);
        Http::fake([$source->ical_url => Http::response($this->ics([
            $this->event('new@example.com', '20260710', '20260715', 'New reservation'),
        ]))]);

        app(CalendarSyncService::class)->sync($source);

        $this->assertDatabaseHas('blocked_dates', ['id' => $manual->id]);
    }

    public function test_does_not_remove_blocked_date_from_another_calendar_source(): void
    {
        $source = $this->createCalendarSource();
        $otherSource = $this->createCalendarSource('Airbnb', 'https://calendar.test/airbnb.ics', $source->rentalUnit);
        $otherBlockedDate = $otherSource->blockedDates()->create([
            'rental_unit_id' => $otherSource->rental_unit_id,
            'source' => 'Airbnb',
            'external_uid' => 'airbnb@example.com',
            'starts_at' => '2026-07-01',
            'ends_at' => '2026-07-03',
            'summary' => 'Other source',
        ]);
        Http::fake([$source->ical_url => Http::response($this->ics([
            $this->event('new@example.com', '20260710', '20260715', 'New reservation'),
        ]))]);

        app(CalendarSyncService::class)->sync($source);

        $this->assertDatabaseHas('blocked_dates', ['id' => $otherBlockedDate->id]);
    }

    public function test_uses_inclusive_start_and_exclusive_end_dates(): void
    {
        $source = $this->createCalendarSource();
        Http::fake([$source->ical_url => Http::response($this->ics([
            $this->event('exclusive@example.com', '20260710', '20260715', 'Exclusive end'),
        ]))]);

        app(CalendarSyncService::class)->sync($source);

        $this->assertTrue(
            $source->rentalUnit->hasBlockedDatesBetween('2026-07-14', '2026-07-15')
        );
        $this->assertFalse(
            $source->rentalUnit->hasBlockedDatesBetween('2026-07-15', '2026-07-16')
        );
    }

    public function test_event_without_dtend_uses_next_day(): void
    {
        $source = $this->createCalendarSource();
        Http::fake([$source->ical_url => Http::response($this->ics([
            "BEGIN:VEVENT\nUID:no-end@example.com\nDTSTART;VALUE=DATE:20260710\nSUMMARY:No end\nEND:VEVENT",
        ]))]);

        $result = app(CalendarSyncService::class)->sync($source);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('blocked_dates', [
            'calendar_source_id' => $source->id,
            'external_uid' => 'no-end@example.com',
            'starts_at' => '2026-07-10 00:00:00',
            'ends_at' => '2026-07-11 00:00:00',
        ]);
    }

    public function test_invalid_http_response_returns_error_without_throwing(): void
    {
        $source = $this->createCalendarSource();
        $oldBlockedDate = $source->blockedDates()->create([
            'rental_unit_id' => $source->rental_unit_id,
            'source' => 'Booking',
            'external_uid' => 'old@example.com',
            'starts_at' => '2026-07-01',
            'ends_at' => '2026-07-03',
            'summary' => 'Old reservation',
        ]);
        Http::fake([$source->ical_url => Http::response('', 500)]);

        $result = app(CalendarSyncService::class)->sync($source);

        $this->assertFalse($result['success']);
        $this->assertNotNull($result['error']);
        $this->assertNull($source->fresh()->last_synced_at);
        $this->assertSame('failed', $source->fresh()->last_sync_status);
        $this->assertNotNull($source->fresh()->last_sync_error);
        $this->assertDatabaseHas('blocked_dates', ['id' => $oldBlockedDate->id]);
    }

    public function test_empty_response_marks_failed_and_does_not_delete_existing_blocked_dates(): void
    {
        $source = $this->createCalendarSource();
        $oldBlockedDate = $this->createSyncedBlockedDate($source);
        Http::fake([$source->ical_url => Http::response('')]);

        $result = app(CalendarSyncService::class)->sync($source);

        $this->assertFalse($result['success']);
        $this->assertSame('failed', $source->fresh()->last_sync_status);
        $this->assertStringContainsString('Calendario vazio recebido', $source->fresh()->last_sync_error);
        $this->assertNull($source->fresh()->last_synced_at);
        $this->assertDatabaseHas('blocked_dates', ['id' => $oldBlockedDate->id]);
    }

    public function test_invalid_content_marks_failed_and_does_not_delete_existing_blocked_dates(): void
    {
        $source = $this->createCalendarSource();
        $oldBlockedDate = $this->createSyncedBlockedDate($source);
        Http::fake([$source->ical_url => Http::response('not an ical calendar')]);

        $result = app(CalendarSyncService::class)->sync($source);

        $this->assertFalse($result['success']);
        $this->assertSame('failed', $source->fresh()->last_sync_status);
        $this->assertSame('Conteudo iCal invalido.', $source->fresh()->last_sync_error);
        $this->assertNull($source->fresh()->last_synced_at);
        $this->assertDatabaseHas('blocked_dates', ['id' => $oldBlockedDate->id]);
    }

    public function test_valid_empty_calendar_marks_failed_and_does_not_delete_existing_blocked_dates(): void
    {
        $source = $this->createCalendarSource();
        $oldBlockedDate = $this->createSyncedBlockedDate($source);
        Http::fake([$source->ical_url => Http::response($this->ics([]))]);

        $result = app(CalendarSyncService::class)->sync($source);

        $this->assertFalse($result['success']);
        $this->assertSame('failed', $source->fresh()->last_sync_status);
        $this->assertStringContainsString('Bloqueios existentes nao foram removidos', $source->fresh()->last_sync_error);
        $this->assertNull($source->fresh()->last_synced_at);
        $this->assertDatabaseHas('blocked_dates', ['id' => $oldBlockedDate->id]);
    }

    public function test_http_exception_marks_failed_without_updating_last_synced_at(): void
    {
        $source = $this->createCalendarSource();
        Http::fake(fn () => throw new ConnectionException('timeout'));

        $result = app(CalendarSyncService::class)->sync($source);

        $this->assertFalse($result['success']);
        $this->assertSame('failed', $source->fresh()->last_sync_status);
        $this->assertStringContainsString('Erro HTTP', $source->fresh()->last_sync_error);
        $this->assertNull($source->fresh()->last_synced_at);
    }

    public function test_success_clears_previous_sync_error_and_updates_last_synced_at(): void
    {
        $source = $this->createCalendarSource();
        $source->forceFill([
            'last_sync_status' => 'failed',
            'last_sync_error' => 'Erro anterior.',
        ])->save();
        Http::fake([$source->ical_url => Http::response($this->ics([
            $this->event('success@example.com', '20260710', '20260715', 'Successful reservation'),
        ]))]);

        $result = app(CalendarSyncService::class)->sync($source);

        $this->assertTrue($result['success']);
        $this->assertSame('success', $source->fresh()->last_sync_status);
        $this->assertNull($source->fresh()->last_sync_error);
        $this->assertNotNull($source->fresh()->last_synced_at);
    }

    public function test_public_unit_page_shows_imported_blocked_date(): void
    {
        $source = $this->createCalendarSource();
        Http::fake([$source->ical_url => Http::response($this->ics([
            $this->event('public@example.com', '20260710', '20260715', 'Public reservation'),
        ]))]);

        app(CalendarSyncService::class)->sync($source);

        $this
            ->get(route('houses.units.show', [$source->rentalUnit->house, $source->rentalUnit]))
            ->assertOk()
            ->assertSee('10/07/2026')
            ->assertSee('Booking');
    }

    private function createCalendarSource(string $platform = 'Booking', string $url = 'https://calendar.test/booking.ics', ?RentalUnit $unit = null): CalendarSource
    {
        $unit ??= $this->createRentalUnit();

        return $unit->calendarSources()->create([
            'platform' => $platform,
            'ical_url' => $url,
            'is_active' => true,
        ]);
    }

    private function createRentalUnit(): RentalUnit
    {
        $house = House::query()->create([
            'name' => 'Casa Teste',
            'slug' => 'casa-teste',
            'short_description' => 'Casa de teste.',
            'description' => 'Casa criada para testes.',
            'location' => 'Geres',
            'is_active' => true,
            'featured' => false,
        ]);

        return $house->rentalUnits()->create([
            'name' => 'T1 Teste',
            'slug' => 't1-teste',
            'type' => 'T1',
            'short_description' => 'Unidade de teste.',
            'description' => 'Unidade criada para testes.',
            'capacity' => 2,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'base_price' => 80,
            'is_active' => true,
            'featured' => false,
        ]);
    }

    private function createSyncedBlockedDate(CalendarSource $source): BlockedDate
    {
        return $source->blockedDates()->create([
            'rental_unit_id' => $source->rental_unit_id,
            'source' => $source->platform,
            'external_uid' => 'old@example.com',
            'starts_at' => '2026-07-01',
            'ends_at' => '2026-07-03',
            'summary' => 'Old reservation',
        ]);
    }

    private function ics(array $events): string
    {
        return "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//Test Calendar//EN\n".implode("\n", $events)."\nEND:VCALENDAR";
    }

    private function event(string $uid, string $startsAt, string $endsAt, string $summary): string
    {
        return "BEGIN:VEVENT\nUID:{$uid}\nDTSTART;VALUE=DATE:{$startsAt}\nDTEND;VALUE=DATE:{$endsAt}\nSUMMARY:{$summary}\nEND:VEVENT";
    }
}
