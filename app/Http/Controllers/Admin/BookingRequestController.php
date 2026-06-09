<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedDate;
use App\Models\BookingRequest;
use App\Models\House;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingRequestController extends Controller
{
    public const STATUSES = [
        'pending' => 'Pendente',
        'contacted' => 'Contactado',
        'confirmed' => 'Confirmado',
        'cancelled' => 'Cancelado',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');
        $houseId = $request->query('house_id');

        $bookingRequests = BookingRequest::query()
            ->with(['rentalUnit.house'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when(array_key_exists((string) $status, self::STATUSES), fn ($query) => $query->where('status', $status))
            ->when($houseId, fn ($query) => $query->whereHas('rentalUnit', fn ($query) => $query->where('house_id', $houseId)))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.booking-requests.index', [
            'bookingRequests' => $bookingRequests,
            'houses' => House::query()->orderBy('name')->get(),
            'search' => $search,
            'status' => $status,
            'houseId' => $houseId,
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(BookingRequest $bookingRequest): View
    {
        $bookingRequest->load('rentalUnit.house');

        return view('admin.booking-requests.show', [
            'bookingRequest' => $bookingRequest,
            'statuses' => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, BookingRequest $bookingRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
        ]);

        if ($data['status'] === 'confirmed') {
            $error = $this->confirmBookingRequest($bookingRequest);

            if ($error) {
                return back()
                    ->withErrors(['status' => $error])
                    ->withInput();
            }

            return back()->with('status', 'Pedido de reserva confirmado e datas bloqueadas com sucesso.');
        }

        $bookingRequest->update(['status' => $data['status']]);

        return back()->with('status', 'Estado do pedido de reserva atualizado com sucesso.');
    }

    public function destroy(BookingRequest $bookingRequest): RedirectResponse
    {
        $bookingRequest->delete();

        return redirect()
            ->route('admin.booking-requests.index')
            ->with('status', 'Pedido de reserva apagado com sucesso.');
    }

    private function confirmBookingRequest(BookingRequest $bookingRequest): ?string
    {
        $bookingRequest->loadMissing('rentalUnit');

        if (! $bookingRequest->rentalUnit) {
            return 'A unidade associada a este pedido ja nao existe.';
        }

        $externalUid = $this->directBookingExternalUid($bookingRequest);

        $existingDirectBlock = BlockedDate::query()
            ->where('source', 'Direct')
            ->where('external_uid', $externalUid)
            ->first();

        if ($existingDirectBlock) {
            $bookingRequest->update(['status' => 'confirmed']);

            return null;
        }

        if ($bookingRequest->rentalUnit->hasBlockedDatesBetween($bookingRequest->check_in, $bookingRequest->check_out)) {
            return 'As datas deste pedido ja estao bloqueadas para esta unidade. O pedido nao foi confirmado.';
        }

        DB::transaction(function () use ($bookingRequest, $externalUid): void {
            $bookingRequest->update(['status' => 'confirmed']);

            BlockedDate::query()->firstOrCreate(
                [
                    'source' => 'Direct',
                    'external_uid' => $externalUid,
                ],
                [
                    'rental_unit_id' => $bookingRequest->rental_unit_id,
                    'calendar_source_id' => null,
                    'starts_at' => $bookingRequest->check_in->toDateString(),
                    'ends_at' => $bookingRequest->check_out->toDateString(),
                    'summary' => "Reserva direta confirmada: {$bookingRequest->name}",
                ]
            );
        });

        return null;
    }

    private function directBookingExternalUid(BookingRequest $bookingRequest): string
    {
        return "direct-booking-request-{$bookingRequest->id}";
    }
}
