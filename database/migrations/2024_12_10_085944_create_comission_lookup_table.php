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
        Schema::create('comission_lookup', function (Blueprint $table) {
            $table->id();
            $table->decimal('bonus_amount');
            $table->decimal('range_min');
            $table->decimal('range_max')->nullable();
            $table->string('description')->nullable();
        });

        DB::table('comission_lookup')->insert([
            ['bonus_amount' => 5, 'range_min' => 100, 'range_max' => 199],
            ['bonus_amount' => 10, 'range_min' => 200, 'range_max' => 299],
            ['bonus_amount' => 15, 'range_min' => 300, 'range_max' => 399],
            ['bonus_amount' => 20, 'range_min' => 400, 'range_max' => 499],
            ['bonus_amount' => 25, 'range_min' => 500, 'range_max' => null],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comission_lookup');
    }
};
