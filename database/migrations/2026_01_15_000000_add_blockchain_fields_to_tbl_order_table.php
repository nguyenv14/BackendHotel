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
        Schema::table('tbl_order', function (Blueprint $table) {
            $table->string('invoice_hash', 64)->nullable()->after('checkin_code')->comment('SHA256 hash của thông tin đơn hàng');
            $table->string('blockchain_tx_hash', 66)->nullable()->after('invoice_hash')->comment('Transaction hash trên blockchain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_order', function (Blueprint $table) {
            $table->dropColumn(['invoice_hash', 'blockchain_tx_hash']);
        });
    }
};

