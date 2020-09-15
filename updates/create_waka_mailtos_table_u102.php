<?php namespace Waka\Mailtoer\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
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
