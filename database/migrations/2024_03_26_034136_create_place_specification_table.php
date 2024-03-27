<?php

use App\Models\Place;
use App\Models\Specification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('place_specification', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Place::class);
            $table->foreignIdFor(Specification::class);
            $table->string('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('place_specification');
    }
};
