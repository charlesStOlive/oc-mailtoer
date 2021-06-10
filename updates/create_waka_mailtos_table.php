<?php namespace Waka\Mailtoer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateWakaMailtosTable extends Migration
{
    public function up()
    {
        Schema::create('waka_mailtoer_waka_mailtos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->text('template')->nullable();
            $table->string('subject');
            $table->string('data_source')->nullable();
            $table->text('scopes')->nullable();
            $table->text('model_functions')->nullable();
            $table->text('images')->nullable();
            $table->integer('sort_order')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_mailtoer_waka_mailtos');
    }
}
