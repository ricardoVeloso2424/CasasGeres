<?php

namespace Database\Seeders;

use App\Models\BookingRequest;
use App\Models\RentalUnit;
use Illuminate\Database\Seeder;

class BookingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unit = RentalUnit::query()->where('slug', 't1-a')->first();

        if (! $unit) {
            return;
        }

        BookingRequest::query()->updateOrCreate(
            [
                'rental_unit_id' => $unit->id,
                'email' => 'maria@example.com',
                'check_in' => now()->addDays(45)->toDateString(),
            ],
            [
                'name' => 'Maria Silva',
                'phone' => '+351 910 000 000',
                'check_out' => now()->addDays(48)->toDateString(),
                'guests' => 2,
                'message' => 'Gostava de confirmar disponibilidade para uma escapadinha no Gerês.',
                'status' => 'pending',
            ]
        );

        BookingRequest::query()->updateOrCreate(
            [
                'rental_unit_id' => $unit->id,
                'phone' => '+351 920 000 000',
                'check_in' => now()->addDays(60)->toDateString(),
            ],
            [
                'name' => 'João Martins',
                'email' => null,
                'check_out' => now()->addDays(63)->toDateString(),
                'guests' => 3,
                'message' => 'Pedido de informação para estadia em família.',
                'status' => 'contacted',
            ]
        );
    }
}
