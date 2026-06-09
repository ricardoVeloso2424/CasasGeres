<?php

namespace Tests\Feature;

use App\Models\CalendarSource;
use App\Models\House;
use App\Models\RentalUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncCalendarsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendars_sync_command_syncs_active_sources(): void
    {
        $source = $this->createCalendarSource('Booking', 'https://calendar.test/booking.ics');
        Http::fake([$source->ical_url => Http::response($this->ics([
            $this->event('booking@example.com', '20260710', '20260715', 'Booking reservation'),
        ]))]);

        $this
            ->artisan('calendars:sync')
            ->assertExitCode(0);

        $this->assertDatabaseHas('blocked_dates', [
            'calendar_source_id' => $source->id,
            'external_uid' => 'booking@example.com',
        ]);
    }

    public function test_calendars_sync_command_source_option_syncs_only_one_source(): void
    {
        $source = $this->createCalendarSource('Booking', 'https://calendar.test/booking.ics');
        $otherSource = $this->createCalendarSource('Airbnb', 'https://calendar.test/airbnb.ics', $source->rentalUnit);
        Http::fake([
            $source->ical_url => Http::response($this->ics([
                $this->event('booking@example.com', '20260710', '20260715', 'Booking reservation'),
            ])),
            $otherSource->ical_url => Http::response($this->ics([
                $this->event('airbnb@example.com', '20260810', '20260815', 'Airbnb reservation'),
            ])),
        ]);

        $this
            ->artisan('calendars:sync', ['--source' => $source->id])
            ->assertExitCode(0);

        $this->assertDatabaseHas('blocked_dates', [
            'calendar_source_id' => $source->id,
            'external_uid' => 'booking@example.com',
        ]);
        $this->assertDatabaseMissing('blocked_dates', [
            'calendar_source_id' => $otherSource->id,
            'external_uid' => 'airbnb@example.com',
        ]);
    }

    public function test_failing_source_does_not_stop_other_sources(): void
    {
        $failingSource = $this->createCalendarSource('Booking', 'https://calendar.test/fail.ics');
        $workingSource = $this->createCalendarSource('Airbnb', 'https://calendar.test/success.ics', $failingSource->rentalUnit);
        Http::fake([
            $failingSource->ical_url => Http::response('', 500),
            $workingSource->ical_url => Http::response($this->ics([
                $this->event('airbnb@example.com', '20260810', '20260815', 'Airbnb reservation'),
            ])),
        ]);

        $this
            ->artisan('calendars:sync')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('blocked_dates', [
            'calendar_source_id' => $failingSource->id,
        ]);
        $this->assertDatabaseHas('blocked_dates', [
            'calendar_source_id' => $workingSource->id,
            'external_uid' => 'airbnb@example.com',
        ]);
        $this->assertSame('failed', $failingSource->fresh()->last_sync_status);
        $this->assertSame('success', $workingSource->fresh()->last_sync_status);
    }

    private function createCalendarSource(string $platform, string $url, ?RentalUnit $unit = null): CalendarSource
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

    private function ics(array $events): string
    {
        return "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//Test Calendar//EN\n".implode("\n", $events)."\nEND:VCALENDAR";
    }

    private function event(string $uid, string $startsAt, string $endsAt, string $summary): string
    {
        return "BEGIN:VEVENT\nUID:{$uid}\nDTSTART;VALUE=DATE:{$startsAt}\nDTEND;VALUE=DATE:{$endsAt}\nSUMMARY:{$summary}\nEND:VEVENT";
    }
}
