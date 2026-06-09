<?php

namespace App\Services;

use App\Models\BlockedDate;
use App\Models\CalendarSource;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Kigkonsult\Icalcreator\Vcalendar;
use Throwable;

class CalendarSyncService
{
    public function sync(CalendarSource $calendarSource): array
    {
        $result = [
            'success' => false,
            'created' => 0,
            'updated' => 0,
            'deleted' => 0,
            'skipped' => 0,
            'error' => null,
        ];

        try {
            $response = Http::timeout(15)->get($calendarSource->ical_url);
        } catch (Throwable $exception) {
            return $this->fail($calendarSource, $result, "Erro HTTP ao obter calendario: {$exception->getMessage()}");
        }

        if ($response->failed()) {
            return $this->fail($calendarSource, $result, "Erro HTTP ao obter calendario: {$response->status()}");
        }

        $body = trim((string) $response->body());

        if ($body === '') {
            return $this->fail($calendarSource, $result, $this->emptyCalendarMessage());
        }

        if (! str_contains(strtoupper($body), 'BEGIN:VCALENDAR')) {
            return $this->fail($calendarSource, $result, 'Conteudo iCal invalido.');
        }

        try {
            $calendar = new Vcalendar();
            $calendar->parse($body);
            $events = $calendar->getComponents(Vcalendar::VEVENT) ?: [];
        } catch (Throwable $exception) {
            return $this->fail($calendarSource, $result, "Erro ao interpretar iCal: {$exception->getMessage()}");
        }

        if ($events === []) {
            return $this->fail($calendarSource, $result, $this->emptyCalendarMessage());
        }

        $syncedIds = [];

        foreach ($events as $event) {
            $data = $this->eventData($calendarSource, $event);

            if ($data === null) {
                $result['skipped']++;
                continue;
            }

            $blockedDate = $this->findExistingBlockedDate($calendarSource, $data);
            $attributes = [
                'rental_unit_id' => $data['rental_unit_id'],
                'calendar_source_id' => $data['calendar_source_id'],
                'source' => $data['source'],
                'external_uid' => $data['external_uid'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'summary' => $data['summary'],
            ];

            if ($blockedDate) {
                $blockedDate->fill($attributes);

                if ($blockedDate->isDirty()) {
                    $blockedDate->save();
                    $result['updated']++;
                }
            } else {
                $blockedDate = BlockedDate::query()->create($attributes);
                $result['created']++;
            }

            $syncedIds[] = $blockedDate->id;
        }

        if ($syncedIds === []) {
            return $this->fail($calendarSource, $result, $this->emptyCalendarMessage());
        }

        $deleteQuery = BlockedDate::query()
            ->where('calendar_source_id', $calendarSource->id);

        $result['deleted'] = $deleteQuery->whereKeyNot($syncedIds)->delete();

        $calendarSource->forceFill([
            'last_synced_at' => now(),
            'last_sync_status' => 'success',
            'last_sync_error' => null,
        ])->save();

        $result['success'] = true;

        Log::info('calendar_sync_success', [
            'calendar_source_id' => $calendarSource->id,
            'platform' => $calendarSource->platform,
            'rental_unit_id' => $calendarSource->rental_unit_id,
            'created' => $result['created'],
            'updated' => $result['updated'],
            'deleted' => $result['deleted'],
            'skipped' => $result['skipped'],
        ]);

        return $result;
    }

    private function eventData(CalendarSource $calendarSource, object $event): ?array
    {
        $startsAt = $this->eventDate($event->getDtstart());

        if (! $startsAt) {
            return null;
        }

        $endsAt = $this->eventDate($event->getDtend());

        if (! $endsAt || $endsAt->lessThanOrEqualTo($startsAt)) {
            $endsAt = $startsAt->copy()->addDay();
        }

        $data = [
            'rental_unit_id' => $calendarSource->rental_unit_id,
            'calendar_source_id' => $calendarSource->id,
            'source' => $calendarSource->platform,
            'external_uid' => $this->cleanString($event->getUid()),
            'starts_at' => $startsAt->toDateString(),
            'ends_at' => $endsAt->toDateString(),
            'summary' => $this->cleanString($event->getSummary()),
        ];

        $validator = Validator::make($data, [
            'rental_unit_id' => ['required', 'integer', 'exists:rental_units,id'],
            'calendar_source_id' => ['required', 'integer', 'exists:calendar_sources,id'],
            'source' => ['required', 'string', 'max:100'],
            'external_uid' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'summary' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return null;
        }

        return $data;
    }

    private function findExistingBlockedDate(CalendarSource $calendarSource, array $data): ?BlockedDate
    {
        if ($data['external_uid']) {
            return BlockedDate::query()
                ->where('calendar_source_id', $calendarSource->id)
                ->where('external_uid', $data['external_uid'])
                ->first();
        }

        return BlockedDate::query()
            ->where('calendar_source_id', $calendarSource->id)
            ->whereDate('starts_at', $data['starts_at'])
            ->whereDate('ends_at', $data['ends_at'])
            ->where('summary', $data['summary'])
            ->first();
    }

    private function eventDate(mixed $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value)->startOfDay();
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (Throwable) {
            return null;
        }
    }

    private function cleanString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function fail(CalendarSource $calendarSource, array $result, string $error): array
    {
        $error = $this->shortError($error);
        $result['success'] = false;
        $result['error'] = $error;

        $calendarSource->forceFill([
            'last_sync_status' => 'failed',
            'last_sync_error' => $error,
        ])->save();

        Log::warning('calendar_sync_failed', [
            'calendar_source_id' => $calendarSource->id,
            'platform' => $calendarSource->platform,
            'rental_unit_id' => $calendarSource->rental_unit_id,
            'error' => $error,
        ]);

        return $result;
    }

    private function emptyCalendarMessage(): string
    {
        return 'Calendario vazio recebido. Bloqueios existentes nao foram removidos por seguranca.';
    }

    private function shortError(string $error): string
    {
        return str($error)->limit(500)->toString();
    }
}
