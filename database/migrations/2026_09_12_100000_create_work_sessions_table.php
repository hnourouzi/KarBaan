<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_plan_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['daily_plan_id', 'started_at']);
            $table->index(['daily_plan_id', 'ended_at']);
        });

        $this->backfillFromDailyPlans();
    }

    public function down(): void
    {
        Schema::dropIfExists('work_sessions');
    }

    private function backfillFromDailyPlans(): void
    {
        $plans = DB::table('daily_plans')
            ->orderBy('id')
            ->get(['id', 'started_at', 'closed_at', 'created_at', 'updated_at']);

        foreach ($plans as $plan) {
            $exists = DB::table('work_sessions')
                ->where('daily_plan_id', $plan->id)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('work_sessions')->insert([
                'daily_plan_id' => $plan->id,
                'started_at' => $plan->started_at,
                'ended_at' => $plan->closed_at,
                'created_at' => $plan->created_at,
                'updated_at' => $plan->updated_at,
            ]);
        }
    }
};
