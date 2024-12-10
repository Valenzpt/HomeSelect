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
        Schema::create('employee_specialities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('speciality_id');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('maintenance_employees');
            $table->foreign('speciality_id')->references('id')->on('specialities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_specialities', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['speciality_id']);
        });
        Schema::dropIfExists('employee_specialities');
    }
};
