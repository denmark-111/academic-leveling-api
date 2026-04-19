<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('shop_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->enum('type', ['powerup', 'cosmetic', 'consumable']);
            $table->json('effect')->nullable(); // e.g. {"hint": true, "extra_time": 30}
            $table->string('icon')->nullable();
            $table->boolean('is_consumable')->default(false);
            $table->integer('stock')->nullable(); // null = unlimited
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shop_items');
    }
};