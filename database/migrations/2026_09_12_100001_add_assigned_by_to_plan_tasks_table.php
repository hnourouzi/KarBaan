<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->foreignId('assigned_by')
                ->nullable()
                ->after('daily_plan_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('assigned_note')->nullable()->after('assigned_by');
        });
    }

    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_by');
            $table->dropColumn('assigned_note');
        });
    }
};
