<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('stamp_id');
            $table->unsignedBigInteger('grading_id');
            $table->float('value');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('stamp_id')->references('id')->on('stamps')->onDelete('cascade');
            $table->foreign('grading_id')->references('id')->on('gradings')->onDelete('cascade');
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
        Schema::dropIfExists('collections');
    }
}
