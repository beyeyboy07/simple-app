<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('star_generations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('jumlah');

            $table->unsignedTinyInteger('tipe');

            $table->text('hasil');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('star_generations');
    }
};