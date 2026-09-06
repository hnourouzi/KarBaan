<?php

namespace Database\Seeders;

use App\Enums\DailyPlanStatus;
use App\Enums\NotDoneReason;
use App\Enums\TaskStatus;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'ادمین کاربان',
            'email' => 'admin@karbaan.test',
        ]);

        User::factory()->manager()->create([
            'name' => 'مریم رضایی',
            'email' => 'manager@karbaan.test',
        ]);

        $employees = collect([
            ['name' => 'علی محمدی', 'email' => 'ali@karbaan.test', 'job_title' => 'توسعه‌دهنده'],
            ['name' => 'سارا احمدی', 'email' => 'sara@karbaan.test', 'job_title' => 'طراح'],
            ['name' => 'رضا کریمی', 'email' => 'reza@karbaan.test', 'job_title' => 'پشتیبان'],
        ])->map(fn (array $data) => User::factory()->employee()->create($data));

        $reasons = NotDoneReason::cases();
        $sampleTitles = [
            'بررسی ایمیل‌های کاری',
            'جلسه هماهنگی تیم',
            'پیگیری پروژه جاری',
            'تهیه گزارش پیشرفت',
            'بازبینی مستندات',
            'پاسخ به درخواست مشتری',
        ];

        foreach ($employees as $index => $employee) {
            foreach (range(1, 8) as $daysAgo) {
                $date = Carbon::today()->subDays($daysAgo);
                $startedAt = $date->copy()->setTime(8 + $index, 30);
                $closedAt = $date->copy()->setTime(16 + $index, 15);

                $plan = DailyPlan::factory()->create([
                    'user_id' => $employee->id,
                    'plan_date' => $date->toDateString(),
                    'status' => DailyPlanStatus::Closed,
                    'started_at' => $startedAt,
                    'closed_at' => $closedAt,
                    'hours_worked' => round(abs($startedAt->diffInMinutes($closedAt)) / 60, 2),
                ]);

                foreach (array_slice($sampleTitles, 0, 4) as $position => $title) {
                    $done = $position < 3 || $daysAgo % 3 !== 0;

                    PlanTask::factory()->create([
                        'daily_plan_id' => $plan->id,
                        'title' => $title,
                        'status' => $done ? TaskStatus::Done : TaskStatus::NotDone,
                        'is_extra' => false,
                        'not_done_reason' => $done ? null : $reasons[$daysAgo % count($reasons)],
                        'not_done_note' => ! $done && $reasons[$daysAgo % count($reasons)] === NotDoneReason::Other
                            ? 'نیاز به هماهنگی بیشتر با تیم بود.'
                            : null,
                        'position' => $position + 1,
                    ]);
                }

                if ($daysAgo % 2 === 0) {
                    PlanTask::factory()->extra()->create([
                        'daily_plan_id' => $plan->id,
                        'title' => 'پشتیبانی فوری خارج از برنامه',
                        'position' => 5,
                    ]);
                }
            }
        }
    }
}
