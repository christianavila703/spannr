<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_snapshots', function (Blueprint $table) {
            $table->id();
            $table->dateTime('timestamp');
            $table->decimal('base_mc', 20, 0)->default(11360730245);
            $table->decimal('market_cap', 20, 0)->nullable();
            $table->decimal('index_change', 16, 4)->nullable();
            $table->decimal('index_value', 18, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_snapshots');
    }
};
