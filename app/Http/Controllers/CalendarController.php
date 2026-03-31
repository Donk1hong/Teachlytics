<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index()
    {
        return Inertia::render('Calendar', [
            'events' => Event::all()
        ]);
    }
}
