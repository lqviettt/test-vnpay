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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_id');
            $table->integer('code')->unique();
            $table->unsignedBigInteger('amount');
            $table->string('transaction_no')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('card_type')->nullable();
            $table->string('response_code')->nullable();
            $table->timestamp('pay_date')->nullable();
            $table->enum('status', ['pending', 'success', 'failed']);
            $table->string('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIfExists();
        });
    }
};
