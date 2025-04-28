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
        Schema::create('excon_weapons', function (Blueprint $table) {
            $table->id();
            
            $table->string("name");
            $table->string("type")->nullable("false")->default("surface to surface");
            $table->integer("kind")->default(1);
            $table->integer("domain")->default(1);
            $table->integer("country")->default(1);
            $table->integer("category")->default(1);
            $table->integer("subcategory")->default(1);
            $table->integer("specific")->default(1);
            $table->integer("extra")->default(1);
            $table->decimal("speed")->default(300);
            $table->decimal("range")->default(300);

            $table->json("data")->nullable(true)->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excon_weapons');
    }
};
