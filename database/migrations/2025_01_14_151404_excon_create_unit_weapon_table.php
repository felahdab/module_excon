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
        Schema::create('excon_unit_weapon', function (Blueprint $table) {
            $table->id();
            $table->foreignId("weapon_id")->references("id")->on("excon_weapons");
            $table->foreignId("unit_id")->references("id")->on("excon_units");
            $table->integer("amount")->default(0);
            $table->datetime("timestamp")->nullable(true)->useCurrent();
            
            $table->json("data")->nullable(true);
            
            $table->timestamps();
        });    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excon_unit_weapon');
    }
};
