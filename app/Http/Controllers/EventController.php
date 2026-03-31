<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\EventStore;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function store(EventStore $request)
    {
        $validated = $request->validated();

        Event::create([
            'title' => $validated['title'],
            'start_date' => $validated['date'],
            'color' => $validated['color'],
            'type' => 'event',
        ]);

        return back();
    }
}
