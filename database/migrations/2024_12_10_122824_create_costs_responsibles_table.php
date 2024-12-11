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
        Schema::create('costs_responsibles', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Cliente', 'Propietario', 'HomeSelect']);
            $table->timestamps();
        });
        DB::table('costs_responsibles')->insert([
            ['type'=>'Cliente'],
            ['type'=>'Propietario'],
            ['type'=>'HomeSelect'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costs_responsibles');
    }
};
