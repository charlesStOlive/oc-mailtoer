<?php namespace Waka\Mailtoer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateWakaMailtosTableU102 extends Migration
{
    public function up()
    {
        Schema::table('waka_mailtoer_waka_mailtos', function (Blueprint $table) {
            $table->text('is_scope')->nullable();
        });
    }

    public function down()
    {
        Schema::table('waka_mailtoer_waka_mailtos', function (Blueprint $table) {
            $table->dropColumn('is_scope');
        });
    }
}
