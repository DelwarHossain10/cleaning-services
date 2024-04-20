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
        Schema::create('activity_details', function (Blueprint $table) {
            $table->id();
            $table->integer('activity_id');
            $table->integer('com_details_id');
            $table->integer('user_id');
            $table->integer('status')->comment('0 = start , 1 = break start, 2 = Break end, 3 = complete, 4= cancel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_details');
    }
};
