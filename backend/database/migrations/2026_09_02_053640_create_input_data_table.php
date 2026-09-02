<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('input_data', function (Blueprint $table) {
            $table->id();

            $table->string('nama');
            $table->string('email');
            $table->string('no_hp', 20);
            $table->text('alamat');

            $table->string('provinsi');
            $table->string('kota');
            $table->string('kecamatan');
            $table->string('kelurahan');

            $table->string('level');
            $table->string('range_gaji');
            $table->unsignedBigInteger('gaji');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('input_data');
    }
};