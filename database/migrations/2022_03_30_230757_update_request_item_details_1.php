<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRequestItemDetails1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_item_details', function (Blueprint $table) {
            $table->string('remarks')->nullable();
            $table->boolean('is_rejected')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_item_details', function (Blueprint $table) {
            $table->dropColumn('remarks');
            $table->dropColumn('is_rejected');
        });
    }
}
