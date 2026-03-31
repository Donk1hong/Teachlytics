<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Rating;
use App\Models\Event;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period', 'month');
        $isAdmin = $user->role === 'admin';

        $dateLimit = match ($period) {
            'semester' => now()->subMonths(6),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $kpis = [
            'avg_score' => (float) round(Rating::where('created_at', '>=', $dateLimit)->avg('total_score') ?? 0, 1),
            'curators_count' => User::where('role', 'curator')->count(),
            'groups_evaluated' => $this->getEvaluationProgress($dateLimit),
        ];

        $allCurators = User::where('role', 'curator')
            ->withAvg('groups', 'average_score')
            ->get()
            ->sortByDesc(fn($u) => (float) $u->groups_avg_average_score)
            ->values();

        $topCurators = $allCurators->take(5);

        $rank = $isAdmin ? '-' : $allCurators->search(fn($u) => $u->id === $user->id);
        $diff = 0;

        if ($rank !== '-' && $rank !== false) {
            if ($rank === 0 && $allCurators->count() > 1) {
                $myScore = (float) ($allCurators[0]->groups_avg_average_score ?? 0);
                $scoreBehind = (float) ($allCurators[1]->groups_avg_average_score ?? 0);
                $diff = abs(round($myScore - $scoreBehind, 2));
            } elseif ($rank > 0) {
                $scoreAhead = (float) ($allCurators[$rank - 1]->groups_avg_average_score ?? 0);
                $myScore = (float) ($allCurators[$rank]->groups_avg_average_score ?? 0);
                $diff = abs(round($scoreAhead - $myScore, 2));
            }
            $rank += 1;
        }

        $activitiesQuery = Rating::with(['group', 'user'])->latest();

        if (!$isAdmin) {
            $activitiesQuery->where('user_id', $user->id);
        }

        $activities = $activitiesQuery->take(4)->get()->map(fn($r) => [
            'id' => $r->id,
            'title' => "Оценка группы " . ($r->group->name ?? 'Без названия'),
            'desc' => "Балл: " . $r->total_score . ($isAdmin ? " (Куратор: " . ($r->user->name ?? 'Неизвестно') . ")" : ""),
            'time' => $r->created_at->diffForHumans(),
            'icon' => 'pi-star-fill',
            'color' => 'text-yellow-500',
            'bg' => 'bg-yellow-500/10'
        ]);

        $pendingTasks = Event::latest()->take(4)->get()->map(fn($e) => [
            'id' => $e->id,
            'group' => $e->title ?? $e->name ?? 'Событие',
            'subject' => $e->description ?? 'Из календаря',
            'deadline' => Carbon::parse($e->date ?? $e->start_date ?? $e->created_at)->format('d.m.Y H:i'),
            'urgent' => Carbon::parse($e->date ?? $e->start_date ?? $e->created_at)->isPast()
        ]);

        return Inertia::render('Dashboard', [
            'kpis' => $kpis,
            'topCurators' => $topCurators,
            'currentPeriod' => $period,
            'userStatus' => [
                'rank' => $rank !== false ? $rank : '-',
                'diff' => $diff,
                'indicators' => [
                    'attendance' => (int) $this->getUserIndicator($user, 'attendance', $dateLimit, $isAdmin),
                    'activity' => (int) $this->getUserIndicator($user, 'activity', $dateLimit, $isAdmin),
                    'discipline' => (int) $this->getUserIndicator($user, 'discipline', $dateLimit, $isAdmin),
                ]
            ],
            'activities' => $activities,
            'pendingTasks' => $pendingTasks
        ]);
    }

    public function downloadReport()
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        $query = Rating::with('group');

        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $ratings = $query->get();
        $csvHeader = ['Группа', 'Период', 'Посещаемость', 'Активность', 'Дисциплина', 'Итог', 'Дата'];

        $callback = function() use ($ratings, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $csvHeader, ';');

            foreach ($ratings as $r) {
                fputcsv($file, [
                    $r->group->name ?? '-',
                    $r->period,
                    $r->attendance,
                    $r->activity,
                    $r->discipline,
                    $r->total_score,
                    $r->created_at->format('Y-m-d H:i')
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=report_" . now()->format('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }

    private function getEvaluationProgress($dateLimit)
    {
        $total = Group::count();
        if ($total === 0) return 0;

        $evaluated = Rating::where('created_at', '>=', $dateLimit)->distinct('group_id')->count();
        return round(($evaluated / $total) * 100);
    }

    private function getUserIndicator($user, $type, $dateLimit, $isAdmin)
    {
        $query = Rating::where('created_at', '>=', $dateLimit);

        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        return $query->avg($type) ?? 0;
    }
}
