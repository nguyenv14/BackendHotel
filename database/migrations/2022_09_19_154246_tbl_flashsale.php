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
        Schema::create('tbl_flashsale', function (Blueprint $table) {
            $table->increments('flashsale_id');
            $table->String('product_id');
            $table->text('flashsale_condition');
            $table->string('flashsale_price_sale');
            $table->String('flashsale_product_price');
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
        Schema::dropIfExists('tbl_flashsale');
    }
};
