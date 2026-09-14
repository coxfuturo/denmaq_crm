<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('alternate_phone')->nullable();
            $table->string('source')->nullable();
            $table->string('service')->nullable();
            $table->string('status')->default('New');
            $table->foreignId('assigned_to')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

            $table->date('follow_up_date')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
            $table->index('source');
            $table->index('assigned_to');
            $table->index('created_by');
            $table->index('follow_up_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
