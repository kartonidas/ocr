<?php

use App\Enums\MatchingRuleType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matching_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('type', array_column(MatchingRuleType::cases(), 'value'));
            $table->integer('product_id');
            $table->integer('priority');
            $table->text('rule');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matching_rules');
    }
};
