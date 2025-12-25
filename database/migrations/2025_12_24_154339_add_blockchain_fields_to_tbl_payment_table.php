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
        // Kiểm tra và thêm cột transaction_hash
        if (!Schema::hasColumn('tbl_payment', 'transaction_hash')) {
            Schema::table('tbl_payment', function (Blueprint $table) {
                $table->string('transaction_hash', 255)->nullable()->after('payment_status');
            });
        }
        
        // Kiểm tra và thêm cột payment_amount_eth
        if (!Schema::hasColumn('tbl_payment', 'payment_amount_eth')) {
            Schema::table('tbl_payment', function (Blueprint $table) {
                $table->decimal('payment_amount_eth', 18, 8)->nullable()->after('transaction_hash');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_payment', function (Blueprint $table) {
            // Xóa cột khi rollback migration
            if (Schema::hasColumn('tbl_payment', 'payment_amount_eth')) {
                $table->dropColumn('payment_amount_eth');
            }
            
            if (Schema::hasColumn('tbl_payment', 'transaction_hash')) {
                $table->dropColumn('transaction_hash');
            }
        });
    }
};
