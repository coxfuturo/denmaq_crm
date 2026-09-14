<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
            ->nullable()
            ->constrained('clients')
            ->nullOnDelete();

            $table->string('project_code')
            ->unique();

            $table->string('name');

            $table->text('description')
            ->nullable();

            $table->date('start_date')
            ->nullable();

            $table->date('end_date')
            ->nullable();

            $table->decimal('budget', 15, 2)
            ->nullable();

            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent'
            ])->default('medium');

            $table->enum('status', [
                'planning',
                'in_progress',
                'on_hold',
                'completed',
                'cancelled'
            ])->default('planning');

            $table->foreignId('assigned_to')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

            $table->foreignId('created_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

            $table->foreignId('updated_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};