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
        Schema::create('organization_financial_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->string('record_id')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('timeframe');
            $table->string('fiscal_period', 4);
            $table->string('fiscal_year', 4);
            $table->integer('cik');
            $table->integer('sic');
            $table->json('tickers');
            $table->string('company_name');
            $table->json('financials');
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
        Schema::dropIfExists('organization_financial_statements');
    }
};
