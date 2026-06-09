<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\AmenityController as AdminAmenityController;
use App\Http\Controllers\Admin\BlockedDateController as AdminBlockedDateController;
use App\Http\Controllers\Admin\BookingRequestController as AdminBookingRequestController;
use App\Http\Controllers\Admin\CalendarSourceController as AdminCalendarSourceController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HouseController as AdminHouseController;
use App\Http\Controllers\Admin\PhotoController as AdminPhotoController;
use App\Http\Controllers\Admin\RentalUnitController as AdminRentalUnitController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BookingRequestController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::scopeBindings()->group(function (): void {
    Route::get('/casas', [HouseController::class, 'index'])->name('houses.index');
    Route::get('/casas/{house:slug}', [HouseController::class, 'show'])->name('houses.show');
    Route::get('/casas/{house:slug}/{unit:slug}', [HouseController::class, 'unit'])->name('houses.units.show');
});

Route::get('/atividades', [ActivityController::class, 'index'])->name('activities.index');
Route::get('/contactos', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contactos', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::post('/pedido-reserva', [BookingRequestController::class, 'store'])->middleware('throttle:5,1')->name('booking-requests.store');
Route::get('/sobre', [PageController::class, 'about'])->name('pages.about');
Route::get('/perguntas-frequentes', [PageController::class, 'faq'])->name('pages.faq');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('houses', AdminHouseController::class)->except(['show']);

        Route::get('/rental-units', [AdminRentalUnitController::class, 'index'])->name('rental-units.index');
        Route::get('/rental-units/create', [AdminRentalUnitController::class, 'create'])->name('rental-units.create');
        Route::post('/rental-units', [AdminRentalUnitController::class, 'store'])->name('rental-units.store');
        Route::get('/rental-units/{rentalUnit:id}/edit', [AdminRentalUnitController::class, 'edit'])->name('rental-units.edit');
        Route::match(['put', 'patch'], '/rental-units/{rentalUnit:id}', [AdminRentalUnitController::class, 'update'])->name('rental-units.update');
        Route::delete('/rental-units/{rentalUnit:id}', [AdminRentalUnitController::class, 'destroy'])->name('rental-units.destroy');

        Route::resource('amenities', AdminAmenityController::class)->except(['show']);
        Route::resource('activities', AdminActivityController::class)->except(['show']);

        Route::post('/photos/{type}/{id}', [AdminPhotoController::class, 'store'])->name('photos.store');
        Route::patch('/photos/{photo}', [AdminPhotoController::class, 'update'])->name('photos.update');
        Route::patch('/photos/{photo}/cover', [AdminPhotoController::class, 'cover'])->name('photos.cover');
        Route::delete('/photos/{photo}', [AdminPhotoController::class, 'destroy'])->name('photos.destroy');

        Route::get('/booking-requests', [AdminBookingRequestController::class, 'index'])->name('booking-requests.index');
        Route::get('/booking-requests/{bookingRequest}', [AdminBookingRequestController::class, 'show'])->name('booking-requests.show');
        Route::patch('/booking-requests/{bookingRequest}/status', [AdminBookingRequestController::class, 'updateStatus'])->name('booking-requests.update-status');
        Route::delete('/booking-requests/{bookingRequest}', [AdminBookingRequestController::class, 'destroy'])->name('booking-requests.destroy');

        Route::get('/contact-messages', [AdminContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::patch('/contact-messages/{contactMessage}/status', [AdminContactMessageController::class, 'updateStatus'])->name('contact-messages.update-status');
        Route::delete('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

        Route::post('/calendar-sources/{calendarSource}/sync', [AdminCalendarSourceController::class, 'sync'])->middleware('throttle:3,1')->name('calendar-sources.sync');
        Route::resource('calendar-sources', AdminCalendarSourceController::class)
            ->except(['show'])
            ->parameters(['calendar-sources' => 'calendarSource']);
        Route::resource('blocked-dates', AdminBlockedDateController::class)
            ->except(['show'])
            ->parameters(['blocked-dates' => 'blockedDate']);
    });
