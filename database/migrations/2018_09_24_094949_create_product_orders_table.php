<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->index();
            $table->integer('user_id')->index();
            $table->string('product_code');
            $table->string('title');
            $table->string('price');
            $table->longText('description');
            $table->integer('qty');
            $table->enum('rating', ['0', '1', '2', '3', '4', '5']);
            $table->enum('show_in_slider', ['0', '1']);
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
        Schema::dropIfExists('product_orders');
    }
}
