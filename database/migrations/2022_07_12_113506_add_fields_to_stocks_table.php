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
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('status')->default(\App\Models\Stock::STATUS_ACTIVE)->after('name');
            $table->date('date_added')->nullable()->after('organization_id');
            $table->date('date_removed')->nullable()->after('date_added');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('date_added');
            $table->dropColumn('date_removed');
        });
    }
};
