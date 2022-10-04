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
        $stocks = \App\Models\Stock::whereNull('date_added')->get();

        foreach ($stocks as $stock) {
            $stock->date_added = '2022-05-10';
            $stock->save();
        }
    }
};
