<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CuratorsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dept = $request->input('dept');

        $curators = User::where('role', 'curator')
            ->withCount('groups')
            ->withAvg('groups', 'average_score')
            ->when($search, function ($query, $search) {
                $query->where('name', 'ilike', "%{$search}%");
            })
            ->get();

        if ($dept && $dept !== 'Все кафедры') {
            $curators = $curators->filter(function ($user) use ($dept) {
                return $user->groups->pluck('department')->contains($dept);
            })->values();
        }

        $departments = Group::whereNotNull('department')
            ->pluck('department')
            ->unique()
            ->values()
            ->toArray();

        $curators->each(function ($user) {
            $user->department = $user->groups->first()->department ?? null;
        });

        return Inertia::render('Curators', [
            'curators' => $curators,
            'departments' => $departments,
            'filters' => [
                'search' => $search,
                'dept' => $dept ?? 'Все кафедры',
            ],
        ]);
    }

    public function show(User $user)
    {
        if ($user->role !== 'curator') {
            abort(404);
        }

        $user->load(['groups.students', 'groups.ratings']);

        $user->groups_count = $user->groups->count();
        $user->groups_avg_average_score = $user->groups->avg('average_score');
        $user->department = $user->groups->first()->department ?? null;

        $recentRatings = $user->groups->flatMap->ratings
            ->sortByDesc('created_at')
            ->take(5)
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'group_name' => $r->group->name,
                    'score' => $r->total_score,
                    'date' => $r->created_at->format('d.m.Y'),
                ];
            })->values();

        $previousUrl = url()->previous();
        $backUrl = $previousUrl !== url()->current() ? $previousUrl : route('curators');

        return Inertia::render('CuratorProfile', [
            'curator' => $user,
            'recentRatings' => $recentRatings,
            'backUrl' => $backUrl,
        ]);
    }
}
