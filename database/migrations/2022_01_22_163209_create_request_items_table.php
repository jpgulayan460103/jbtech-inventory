<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->unsignedBigInteger('requester_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('processor_id')->nullable();
            $table->string('status')->nullable();
            $table->string('request_type')->nullable();
            $table->string('request_number')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('received_id')->nullable();
            $table->dateTime('received_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('processor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('received_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_items');
    }
}
