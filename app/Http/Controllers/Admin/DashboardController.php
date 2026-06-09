<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\BlockedDate;
use App\Models\BookingRequest;
use App\Models\CalendarSource;
use App\Models\ContactMessage;
use App\Models\House;
use App\Models\RentalUnit;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'houses' => House::query()->count(),
                'rentalUnits' => RentalUnit::query()->count(),
                'pendingBookingRequests' => BookingRequest::query()->where('status', 'pending')->count(),
                'unreadContactMessages' => ContactMessage::query()->where('status', 'unread')->count(),
                'activeActivities' => Activity::query()->where('is_active', true)->count(),
                'activeCalendarSources' => CalendarSource::query()->where('is_active', true)->count(),
                'futureBlockedDates' => BlockedDate::query()->where('ends_at', '>=', today()->toDateString())->count(),
            ],
            'nextBlockedDate' => BlockedDate::query()
                ->with(['rentalUnit.house', 'calendarSource'])
                ->where('ends_at', '>=', today()->toDateString())
                ->orderBy('starts_at')
                ->first(),
            'latestBookingRequests' => BookingRequest::query()
                ->with(['rentalUnit.house'])
                ->latest()
                ->limit(5)
                ->get(),
            'latestContactMessages' => ContactMessage::query()
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
