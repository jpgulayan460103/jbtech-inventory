<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestItemDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_item_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('per_piece')->nullable();
            $table->unsignedBigInteger('request_item_id')->nullable();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('request_item_id')->references('id')->on('request_items')->onDelete('cascade');
            $table->integer('fulfilled_quantity')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_item_details');
    }
}
