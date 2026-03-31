<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RatingsController;
use App\Http\Controllers\CuratorsController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/report', [DashboardController::class, 'downloadReport'])->name('dashboard.report');

    Route::get('/ratings', [RatingsController::class, 'index'])->name('ratings.index');
    Route::post('/ratings/save', [RatingsController::class, 'store'])->name('ratings.store');

    Route::get('/curators', [CuratorsController::class, 'index'])->name('curators');
    Route::get('/curators/{user}', [CuratorsController::class, 'show'])->name('curators.show');

    Route::get('/messages', [MessagesController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessagesController::class, 'store'])->name('messages.store');
    Route::post('/messages/start/{user}', [MessagesController::class, 'startConversation'])->name('messages.start');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');

    Route::get('/support', [SupportController::class, 'index'])->name('support');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::delete('/support/{ticket}', [SupportController::class, 'destroy'])->name('support.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/notifications/clear', [NotificationController::class, 'clearAll'])->name('notifications.clear');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/manage', [CuratorsController::class, 'manage'])->name('admin.manage');
        Route::post('/admin/reset-period', [RatingsController::class, 'reset'])->name('admin.reset');
    });
});

require __DIR__.'/auth.php';
