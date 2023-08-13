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
        Schema::create('user_stats', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users');
	    $table->unsignedInteger('level')->default(1);
            $table->unsignedInteger('experience')->default(0);
            $table->unsignedInteger('strength')->default(10);
            $table->unsignedInteger('agility')->default(10);
            $table->unsignedInteger('guard')->default(10);
            $table->unsignedInteger('labor')->default(10);
            $table->unsignedInteger('iq')->default(10);
            $table->unsignedInteger('energy')->default(10);
            $table->unsignedInteger('maxEnergy')->default(10);
            $table->unsignedInteger('will')->default(100);
            $table->unsignedInteger('maxWill')->default(100);
            $table->unsignedInteger('brave')->default(5);
            $table->unsignedInteger('maxBrave')->default(5);
            $table->unsignedInteger('hp')->default(100);
            $table->unsignedInteger('maxHP')->default(100);
            $table->unsignedInteger('primaryCurrencyHeld')->default(100);
            $table->integer('primaryCurrencyBank')->default(-1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stats');
    }
};
