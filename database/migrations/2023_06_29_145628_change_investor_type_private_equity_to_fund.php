<?php

use App\Models\Investor;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Investor::where('type', 'Private Equity')->update(['type' => Investor::TYPE_FUND]);
    }
};
