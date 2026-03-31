<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Student;
use App\Models\Rating;
use App\Models\Event;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@test.com'], [
            'name' => 'Главный Админ', 'password' => Hash::make('password'), 'role' => 'admin'
        ]);

        $curator = User::updateOrCreate(['email' => 'curator@test.com'], [
            'name' => 'Иван Кураторов', 'password' => Hash::make('password'), 'role' => 'curator'
        ]);

        $group = Group::updateOrCreate(['curator_id' => $curator->id], [
            'name' => 'ИСП-301',
            'department' => 'Кафедра информационных технологий'
        ]);

        $student = Student::updateOrCreate(['group_id' => $group->id], ['name' => 'Студент Тестовый']);

        Rating::create([
            'user_id' => $curator->id, 'group_id' => $group->id, 'student_id' => $student->id,
            'attendance' => 95, 'activity' => 85, 'discipline' => 100, 'total_score' => 4.9,
            'period' => 'month', 'created_at' => now()
        ]);

        Rating::create([
            'user_id' => $curator->id, 'group_id' => $group->id, 'student_id' => $student->id,
            'attendance' => 40, 'activity' => 50, 'discipline' => 60, 'total_score' => 3.0,
            'period' => 'semester', 'created_at' => now()->subMonths(4)
        ]);

        $otherCurators = User::factory(4)->create(['role' => 'curator']);

        $otherCurators->each(function ($c) {
            $g = Group::factory()->create([
                'curator_id' => $c->id,
                'department' => 'Общая кафедра'
            ]);
            $s = Student::factory()->create(['group_id' => $g->id]);

            Rating::factory(2)->create([
                'user_id' => $c->id, 'group_id' => $g->id, 'student_id' => $s->id, 'created_at' => now()->subDays(rand(1, 15))
            ]);

            $g->update(['average_score' => Rating::where('group_id', $g->id)->avg('total_score')]);
        });

        $group->update(['average_score' => Rating::where('group_id', $group->id)->avg('total_score')]);

        Event::factory(3)->create();

        $conversation1 = Conversation::create([
            'user_one' => $admin->id,
            'user_two' => $curator->id,
        ]);

        Message::create([
            'conversation_id' => $conversation1->id,
            'sender_id' => $admin->id,
            'text' => 'Здравствуйте, Иван. Как успехи с заполнением ведомостей?',
            'created_at' => now()->subHours(2)
        ]);

        Message::create([
            'conversation_id' => $conversation1->id,
            'sender_id' => $curator->id,
            'text' => 'Добрый день! Группа ИСП-301 уже почти готова, заполняю последних студентов.',
            'created_at' => now()->subMinutes(45)
        ]);

        $conversation2 = Conversation::create([
            'user_one' => $curator->id,
            'user_two' => $otherCurators->first()->id,
        ]);

        Message::create([
            'conversation_id' => $conversation2->id,
            'sender_id' => $otherCurators->first()->id,
            'text' => 'Привет! Ты уже сдал отчет за этот месяц?',
            'created_at' => now()->subDays(1)
        ]);
    }
}
