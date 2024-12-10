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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incident_id');
            $table->unsignedBigInteger('employee_id'); 
            $table->unsignedBigInteger('status_id'); 
            $table->unsignedBigInteger('responsible_cost_id'); 
            $table->text('description'); 
            $table->decimal('cost', 10, 2); 
            $table->timestamps();
            
            $table->foreign('incident_id')->references('id')->on('incidents');
            $table->foreign('employee_id')->references('id')->on('maintenance_employees');
            $table->foreign('status_id')->references('id')->on('tasks_status');
            $table->foreign('responsible_cost_id')->references('id')->on('costs_responsibles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['incident_id']);
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['status_id']);
            $table->dropForeign(['responsible_cost_id']);
        });
        Schema::dropIfExists('tasks');
    }
};
