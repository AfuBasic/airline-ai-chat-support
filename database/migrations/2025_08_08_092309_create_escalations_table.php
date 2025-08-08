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
        Schema::create('escalations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('escalated_to')->nullable()->comment('The agent to which the conversation is escalated');
            $table->timestamp('escalated_at')->nullable()->comment('The time when the conversation was escalated');
            $table->text('resolved_at')->nullable()->comment('The time when the conversation was resolved');
            $table->text('notes')->nullable()->comment('Additional notes regarding the escalation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalations');
    }
};
