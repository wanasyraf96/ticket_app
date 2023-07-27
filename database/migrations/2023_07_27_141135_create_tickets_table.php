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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('assignor')->nullable()->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('assignee')->nullable()->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->text('description')->nullable();
            $table->char('priority')->nullable();
            $table->char('status')->default(1);
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
