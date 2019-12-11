<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThrottleQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('throttle_queues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('model_id')->nullable();
            $table->string('model_class', 50)->nullable();
            $table->index(['model_id', 'model_class']);
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
        Schema::dropIfExists('throttle_queues');
    }
}
