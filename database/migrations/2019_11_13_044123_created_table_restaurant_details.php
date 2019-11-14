<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedTableRestaurantDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            //$table->string('res_id');
            $table->string('name')->nullable();
            $table->string('branch')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('housenumber')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('url')->nullable();
            $table->string('open')->nullable();
            $table->string('bestMatch')->nullable();
            $table->string('newestScore')->nullable();
            $table->string('ratingAverage')->nullable();
            $table->string('popularity')->nullable();
            $table->string('averageProductPrice')->nullable();
            $table->string('deliveryCosts')->nullable();
            $table->string('minimumOrderAmount')->nullable();
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
        Schema::dropIfExists('restaurant_details');
    }
}
