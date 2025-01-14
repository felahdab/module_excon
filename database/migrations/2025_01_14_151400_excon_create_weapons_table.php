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
            $table->integer("kind")->default(1);
            $table->integer("domain")->default(1);
            $table->integer("country")->default(1);
            $table->integer("category")->default(1);
            $table->integer("subcategory")->default(1);
            $table->integer("specific")->default(1);
            $table->integer("extra")->default(1);

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
