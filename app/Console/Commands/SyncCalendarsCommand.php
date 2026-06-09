<?php

namespace App\Console\Commands;

use App\Models\CalendarSource;
use App\Services\CalendarSyncService;
use Illuminate\Console\Command;
use Throwable;

class SyncCalendarsCommand extends Command
{
    protected $signature = 'calendars:sync {--source= : ID da fonte de calendario a sincronizar}';

    protected $description = 'Sincroniza fontes iCal ativas para datas bloqueadas.';

    public function handle(CalendarSyncService $calendarSyncService): int
    {
        try {
            $query = CalendarSource::query()
                ->with('rentalUnit.house')
                ->where('is_active', true);

            if ($sourceId = $this->option('source')) {
                $query->whereKey($sourceId);
            }

            $calendarSources = $query->orderBy('id')->get();
        } catch (Throwable $exception) {
            $this->error("Erro global ao preparar sincronizacao: {$exception->getMessage()}");

            return self::FAILURE;
        }

        if ($calendarSources->isEmpty()) {
            $this->info('Nao existem fontes ativas para sincronizar.');

            return self::SUCCESS;
        }

        foreach ($calendarSources as $calendarSource) {
            $unit = $calendarSource->rentalUnit;
            $house = $unit?->house;
            $label = "#{$calendarSource->id} {$calendarSource->platform} - {$house?->name} - {$unit?->name}";

            $this->line("Sincronizar {$label}");

            $result = $calendarSyncService->sync($calendarSource);

            if (! $result['success']) {
                $this->warn("Erro: {$result['error']}");
                continue;
            }

            $this->info(
                "OK: criados {$result['created']}, atualizados {$result['updated']}, removidos {$result['deleted']}, ignorados {$result['skipped']}"
            );
        }

        return self::SUCCESS;
    }
}
