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
        Schema::create('router_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_id');
            $table->string('agent_first_name');
            $table->string('agent_middle_name');
            $table->string('agent_last_name');
            $table->string('router_image')->nullable();
            $table->string('zip_path');
            $table->string('organization');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('router_cases');
    }
};
