<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ratings\RatingsStore;
use App\Models\Group;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Rating;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $groupId = $request->input('group_id');
        $period = $request->input('period', 'month');

        $groups = Group::when(!$user->isAdmin(), function ($query) use ($user) {
            return $query->where('curator_id', $user->id);
        })
            ->withCount('students')
            ->get();

        $activeGroup = $groupId ? Group::find($groupId) : $groups->first();

        $students = [];
        if ($activeGroup) {
            $students = $activeGroup->students()
                ->with(['ratings' => function($q) use ($period) {
                    $q->where('period', $period);
                }])
                ->get()
                ->map(function($student) {
                    $rating = $student->ratings->first();
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'attendance' => $rating->attendance ?? 0,
                        'activity' => $rating->activity ?? 0,
                        'discipline' => $rating->discipline ?? 0,
                        'total' => $rating->total_score ?? 0,
                    ];
                });
        }

        return Inertia::render('Ratings/Index', [
            'groups' => $groups,
            'students' => $students,
            'selectedGroupId' => $activeGroup ? $activeGroup->id : null,
            'currentPeriod' => $period
        ]);
    }

    public function store(RatingsStore $request)
    {
        $request->validated();
        $user = Auth::user();

        foreach ($request->students as $data) {
            $total = round(($data['attendance'] + $data['activity'] + $data['discipline']) / 3 / 20, 1);

            Rating::updateOrCreate(
                ['student_id' => $data['id'], 'period' => $request->period, 'group_id' => $request->group_id],
                ['user_id' => $user->id, 'attendance' => $data['attendance'], 'activity' => $data['activity'], 'discipline' => $data['discipline'], 'total_score' => $total]
            );
        }

        $group = Group::with('curator')->find($request->group_id);
        $avg = round(Rating::where('group_id', $group->id)->where('period', $request->period)->avg('total_score'), 1);

        $group->update(['average_score' => $avg]);

        if ($group->curator_id) {
            Notification::create([
                'user_id' => $group->curator_id,
                'title' => 'Обновление рейтинга',
                'desc' => "Группа {$group->name} получила средний балл {$avg} за период: {$request->period}",
                'type' => 'rating'
            ]);
        }

        return back();
    }
}
