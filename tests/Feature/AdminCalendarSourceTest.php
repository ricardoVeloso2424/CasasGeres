<?php

namespace Tests\Feature;

use App\Models\CalendarSource;
use App\Models\RentalUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminCalendarSourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_calendar_sources(): void
    {
        $this->get('/admin/calendar-sources')->assertRedirect('/login');
    }

    public function test_admin_can_see_calendar_sources_index(): void
    {
        $this->seed();

        $this
            ->actingAsAdmin()
            ->get('/admin/calendar-sources')
            ->assertOk()
            ->assertSee('Gerir fontes de calendario')
            ->assertSee('Booking')
            ->assertSee('Casa do Rio')
            ->assertSee('T1 A');
    }

    public function test_admin_can_create_calendar_source(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-b')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->post(route('admin.calendar-sources.store'), [
                'rental_unit_id' => $unit->id,
                'platform' => 'Vrbo',
                'ical_url' => 'https://example.com/calendars/vrbo/t1-b.ics',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('calendar_sources', [
            'rental_unit_id' => $unit->id,
            'platform' => 'Vrbo',
            'ical_url' => 'https://example.com/calendars/vrbo/t1-b.ics',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_edit_calendar_source(): void
    {
        $this->seed();
        $calendarSource = CalendarSource::query()->where('platform', 'Booking')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->put(route('admin.calendar-sources.update', $calendarSource), [
                'rental_unit_id' => $calendarSource->rental_unit_id,
                'platform' => 'Booking Atualizado',
                'ical_url' => 'https://example.com/calendars/booking/updated.ics',
            ])
            ->assertRedirect(route('admin.calendar-sources.edit', $calendarSource));

        $this->assertDatabaseHas('calendar_sources', [
            'id' => $calendarSource->id,
            'platform' => 'Booking Atualizado',
            'ical_url' => 'https://example.com/calendars/booking/updated.ics',
            'is_active' => false,
        ]);
    }

    public function test_admin_cannot_create_calendar_source_without_rental_unit_id(): void
    {
        $this->seed();

        $this
            ->actingAsAdmin()
            ->post(route('admin.calendar-sources.store'), [
                'platform' => 'Booking',
                'ical_url' => 'https://example.com/calendar.ics',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('rental_unit_id');
    }

    public function test_admin_cannot_create_calendar_source_with_invalid_url(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->post(route('admin.calendar-sources.store'), [
                'rental_unit_id' => $unit->id,
                'platform' => 'Booking',
                'ical_url' => 'not-a-url',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('ical_url');
    }

    public function test_admin_cannot_delete_calendar_source_with_blocked_dates(): void
    {
        $this->seed();
        $calendarSource = CalendarSource::query()->has('blockedDates')->firstOrFail();

        $this
            ->actingAsAdmin()
            ->delete(route('admin.calendar-sources.destroy', $calendarSource))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('calendar_sources', [
            'id' => $calendarSource->id,
        ]);
    }

    public function test_guest_cannot_sync_calendar_source(): void
    {
        $this->seed();
        $calendarSource = CalendarSource::query()->firstOrFail();

        $this
            ->post(route('admin.calendar-sources.sync', $calendarSource))
            ->assertRedirect('/login');
    }

    public function test_admin_can_sync_calendar_source_from_button_route(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $calendarSource = $unit->calendarSources()->create([
            'platform' => 'Booking',
            'ical_url' => 'https://calendar.test/admin-sync.ics',
            'is_active' => true,
        ]);
        Http::fake([$calendarSource->ical_url => Http::response($this->ics([
            $this->event('admin-sync@example.com', '20260710', '20260715', 'Admin sync reservation'),
        ]))]);

        $this
            ->actingAsAdmin()
            ->post(route('admin.calendar-sources.sync', $calendarSource))
            ->assertRedirect()
            ->assertSessionHas('status', 'Fonte sincronizada com sucesso. Criados: 1; atualizados: 0; removidos: 0; ignorados: 0.');

        $this->assertDatabaseHas('blocked_dates', [
            'calendar_source_id' => $calendarSource->id,
            'external_uid' => 'admin-sync@example.com',
        ]);
        $this->assertNotNull($calendarSource->fresh()->last_synced_at);
        $this->assertSame('success', $calendarSource->fresh()->last_sync_status);
        $this->assertNull($calendarSource->fresh()->last_sync_error);
    }

    public function test_last_synced_at_appears_after_successful_sync(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $calendarSource = $unit->calendarSources()->create([
            'platform' => 'Vrbo',
            'ical_url' => 'https://calendar.test/last-sync.ics',
            'is_active' => true,
        ]);
        Http::fake([$calendarSource->ical_url => Http::response($this->ics([
            $this->event('last-sync@example.com', '20260710', '20260715', 'Last sync reservation'),
        ]))]);

        $this->actingAsAdmin()->post(route('admin.calendar-sources.sync', $calendarSource));

        $this
            ->actingAsAdmin()
            ->get('/admin/calendar-sources?search=last-sync')
            ->assertOk()
            ->assertSee('Vrbo')
            ->assertSee('Success')
            ->assertSee($calendarSource->fresh()->last_synced_at->format('d/m/Y H:i'));
    }

    public function test_admin_sync_error_shows_short_feedback_and_status(): void
    {
        $this->seed();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();
        $calendarSource = $unit->calendarSources()->create([
            'platform' => 'Booking',
            'ical_url' => 'https://calendar.test/admin-error.ics',
            'is_active' => true,
        ]);
        Http::fake([$calendarSource->ical_url => Http::response('', 404)]);

        $this
            ->actingAsAdmin()
            ->post(route('admin.calendar-sources.sync', $calendarSource))
            ->assertRedirect()
            ->assertSessionHas('error', 'Sincronizacao falhou: Erro HTTP ao obter calendario: 404');

        $this
            ->actingAsAdmin()
            ->get('/admin/calendar-sources?search=admin-error')
            ->assertOk()
            ->assertSee('Failed')
            ->assertSee('Erro HTTP ao obter calendario: 404');

        $this->assertNull($calendarSource->fresh()->last_synced_at);
        $this->assertSame('failed', $calendarSource->fresh()->last_sync_status);
    }

    private function ics(array $events): string
    {
        return "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//Test Calendar//EN\n".implode("\n", $events)."\nEND:VCALENDAR";
    }

    private function event(string $uid, string $startsAt, string $endsAt, string $summary): string
    {
        return "BEGIN:VEVENT\nUID:{$uid}\nDTSTART;VALUE=DATE:{$startsAt}\nDTEND;VALUE=DATE:{$endsAt}\nSUMMARY:{$summary}\nEND:VEVENT";
    }

    private function actingAsAdmin(): self
    {
        return $this->actingAs(User::query()->where('email', 'admin@example.com')->firstOrFail());
    }
}
