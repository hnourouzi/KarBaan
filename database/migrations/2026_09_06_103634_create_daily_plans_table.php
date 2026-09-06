<?php

use App\Enums\DailyPlanStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('plan_date');
            $table->string('status')->default(DailyPlanStatus::Open->value);
            $table->timestamp('started_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('hours_worked', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'plan_date']);
            $table->index(['plan_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_plans');
    }
};
