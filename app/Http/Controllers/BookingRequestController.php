<?php

namespace App\Http\Controllers;

use App\Mail\NewBookingRequestReceived;
use App\Models\BookingRequest;
use App\Models\RentalUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Throwable;

class BookingRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'rental_unit_id' => ['required', 'integer', 'exists:rental_units,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['nullable', 'integer', 'min:1'],
            'message' => ['nullable', 'string', 'max:5000'],
        ], [
            'check_in.after_or_equal' => 'A data de entrada não pode ser no passado.',
            'check_out.after' => 'A data de saída tem de ser posterior à data de entrada.',
            'rental_unit_id.exists' => 'A unidade selecionada não existe.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            if ($request->filled('website')) {
                $validator->errors()->add('contact', 'Nao foi possivel processar o formulario. Tente novamente.');
            }

            if (! $request->filled('email') && ! $request->filled('phone')) {
                $validator->errors()->add('contact', 'Indique pelo menos email ou telefone para podermos responder.');
            }

            if ($validator->errors()->has('rental_unit_id')) {
                return;
            }

            $unit = RentalUnit::query()->find($request->integer('rental_unit_id'));

            if (! $unit) {
                return;
            }

            if (! $unit->is_active) {
                $validator->errors()->add('rental_unit_id', 'A unidade selecionada não está disponível para pedidos de reserva.');
            }

            if ($request->filled('guests') && $request->integer('guests') > $unit->capacity) {
                $validator->errors()->add('guests', "Esta unidade permite no máximo {$unit->capacity} hóspedes.");
            }

            if (
                ! $validator->errors()->has('check_in')
                && ! $validator->errors()->has('check_out')
                && $unit->hasBlockedDatesBetween($request->input('check_in'), $request->input('check_out'))
            ) {
                $validator->errors()->add(
                    'check_in',
                    'As datas selecionadas já não estão disponíveis para esta unidade. Escolha outras datas ou contacte-nos diretamente.'
                );
            }
        });

        $data = $validator->validate();

        $bookingRequest = BookingRequest::query()->create([
            'rental_unit_id' => $data['rental_unit_id'],
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'check_in' => $data['check_in'],
            'check_out' => $data['check_out'],
            'guests' => $data['guests'] ?? null,
            'message' => $data['message'] ?? null,
            'status' => 'pending',
        ]);

        $this->notifyOwner($bookingRequest);

        return back()
            ->withInput($request->only(['check_in', 'check_out', 'guests']))
            ->with('booking_status', 'O seu pedido de reserva foi enviado com sucesso. A confirmação será feita posteriormente por contacto direto.');
    }

    private function notifyOwner(BookingRequest $bookingRequest): void
    {
        $ownerEmail = trim((string) config('site.email'));

        if ($ownerEmail === '') {
            Log::warning('SITE_EMAIL is not configured; booking request email was not sent.', [
                'booking_request_id' => $bookingRequest->id,
            ]);

            return;
        }

        try {
            Mail::to($ownerEmail)->send(new NewBookingRequestReceived($bookingRequest));
        } catch (Throwable $exception) {
            Log::error('Booking request email could not be sent.', [
                'booking_request_id' => $bookingRequest->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
