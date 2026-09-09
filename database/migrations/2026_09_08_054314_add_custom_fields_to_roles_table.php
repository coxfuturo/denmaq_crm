<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {

            $table->string('name_alias')
                ->nullable()
                ->after('name');

            $table->string('icon')
                ->nullable()
                ->after('name_alias');

            $table->text('description')
                ->nullable()
                ->after('icon');

            $table->integer('position')
                ->default(0)
                ->after('description');

            $table->boolean('status')
                ->default(true)
                ->after('position');

            $table->unsignedBigInteger('created_by')
                ->nullable()
                ->after('status');

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {

            $table->dropSoftDeletes();

            $table->dropColumn([
                'name_alias',
                'icon',
                'description',
                'position',
                'status',
                'created_by',
            ]);

        });
    }
};