<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('order_details', 'orders_id')) {
                $table->unsignedBigInteger('orders_id')->nullable()->after('id');
                $table->foreign('orders_id')->references('id')->on('orders')->onDelete('cascade');
            }
            if (!Schema::hasColumn('order_details', 'products_id')) {
                $table->unsignedBigInteger('products_id')->nullable()->after('orders_id');
                $table->foreign('products_id')->references('id')->on('products')->onDelete('cascade');
            }
            if (!Schema::hasColumn('order_details', 'qty')) {
                $table->integer('qty')->default(1)->after('products_id');
            }
            if (!Schema::hasColumn('order_details', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('qty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('order_details', 'qty')) {
                $table->dropColumn('qty');
            }
            if (Schema::hasColumn('order_details', 'products_id')) {
                $table->dropForeign(['products_id']);
                $table->dropColumn('products_id');
            }
            if (Schema::hasColumn('order_details', 'orders_id')) {
                $table->dropForeign(['orders_id']);
                $table->dropColumn('orders_id');
            }
        });
    }
};
