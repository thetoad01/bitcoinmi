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
        Schema::create('geminis', function (Blueprint $table) {
            $table->id();
            $table->float('bid', 10, 2);
            $table->float('ask', 10, 2);
            $table->float('last', 10, 2);
            $table->float('volume_btc', 15, 8);
            $table->float('volume_usd', 20, 8);
            $table->bigInteger('volume_timestamp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geminis');
    }
};
