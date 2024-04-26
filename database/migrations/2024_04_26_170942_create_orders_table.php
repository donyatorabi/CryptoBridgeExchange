<?php

use App\Modules\Order\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('user_email', 255);
            $table->unsignedBigInteger('src_coin_id');
            $table->unsignedBigInteger('dest_coin_id');
            $table->unsignedInteger('amount');
            $table->unsignedBigInteger('src_coin_price');
            $table->unsignedBigInteger('dest_coin_price')->nullable();
            $table->enum('status', array_values(Order::STATUSES))
                ->default(Order::STATUSES['PENDING']);
            $table->timestamps();

            $table->foreign('src_coin_id')->references('id')->on('coins');
            $table->foreign('dest_coin_id')->references('id')->on('coins');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
