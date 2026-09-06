<?php

use App\Enums\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_plan_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('status')->default(TaskStatus::Planned->value);
            $table->boolean('is_extra')->default(false);
            $table->string('not_done_reason')->nullable();
            $table->text('not_done_note')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['daily_plan_id', 'position']);
            $table->index(['daily_plan_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_tasks');
    }
};
