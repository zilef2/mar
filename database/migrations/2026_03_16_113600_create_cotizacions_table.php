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
        Schema::create('cotizacions', function (Blueprint $table) {
            $table->string('engineer_name')->nullable();
            $table->string('company')->nullable();
            $table->string('project')->nullable();
            $table->date('date')->nullable();
            $table->integer('subtotal')->nullable();
            $table->integer('tax_rate')->nullable();
            $table->integer('tax_amount')->nullable();
            $table->integer('total')->nullable();
            $table->string('status')->nullable();
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacions');
    }
};
