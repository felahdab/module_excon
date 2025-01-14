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
        Schema::create('excon_positions', function (Blueprint $table) {
            $table->id();
            $table->datetime("timestamp")->nullable(false)->useCurrent();
            $table->foreignId("unit_id")->references("id")->on("excon_units")->nullable(false);
            
            $table->string("source")->nullable(false);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            $table->json("data")->nullable(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excon_positions');
    }
};
