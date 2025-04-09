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
        Schema::create('excon_user_units', function (Blueprint $table) {
            $table->id();            
            $table->foreignId("user_id")->references("id")->on("users");
            $table->foreignId("unit_id")->references("id")->on("excon_units");
            
            $table->json("data")->nullable(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excon_user_units');
    }
};
