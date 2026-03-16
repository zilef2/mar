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
        Schema::create('interruptores', function (Blueprint $table) {
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->integer('value')->nullable();
            $table->integer('discounted_value')->nullable();
            $table->integer('unit_price')->nullable();
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interruptores');
    }
};
