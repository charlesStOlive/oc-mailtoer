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
            $table->string('subject')->nullable();
            $table->string('state')->default('Actif');
            $table->boolean('render_mode_html')->nullable();
            $table->text('content')->nullable();
            $table->string('test_id')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->string('data_source');
            //reorder
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_mailtoer_waka_mailtos');
    }
}