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
        if (!Schema::hasTable('reservation_items')) {
            Schema::create('reservation_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reservation_id')->constrained();
                $table->foreignId('seat_id')->constrained();
                $table->decimal('price', 8, 2);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_items');
    }
};
