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
        Schema::table('form_entries', function (Blueprint $table) {
            $table->string('mailchimp_status')->nullable()->after('user_id');
            $table->json('mailchimp_response')->nullable()->after('mailchimp_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_entries', function (Blueprint $table) {
            $table->dropColumn('mailchimp_status');
            $table->dropColumn('mailchimp_response');
        });
    }
};
