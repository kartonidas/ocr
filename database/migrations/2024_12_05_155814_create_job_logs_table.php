<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_logs', function (Blueprint $table) {
            $table->id();
            $table->text('job');
            $table->tinyInteger('success');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_logs');
    }
};
