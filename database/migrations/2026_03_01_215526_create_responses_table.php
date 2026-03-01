<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_hash', 64)->index(); // منع تكرار بسيط
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->unique(['survey_id','user_id']);   // منع تكرار للـ user (لو مسجل)
            $table->unique(['survey_id','ip_hash']);   // ومنع تكرار للـ IP
        });
    }
    public function down(): void {
        Schema::dropIfExists('responses');
    }
};