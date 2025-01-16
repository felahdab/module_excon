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
        Schema::create('excon_identifiers', function (Blueprint $table) {
            $table->id();
            $table->string("source");
            $table->string("identifier");
            $table->foreignId("unit_id")->nullable(true)->references("id")->on("excon_units")->default(null);

            $table->json("data")->nullable(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excon_identifiers');
    }
};
