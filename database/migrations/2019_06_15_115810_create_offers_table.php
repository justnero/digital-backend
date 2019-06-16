<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'offers', function ( Blueprint $table ) {
            $table->bigIncrements( 'id' );
            $table->string( 'type' );
            $table->string( 'address' );
            $table->decimal( 'longitude', 20, 16 );
            $table->decimal( 'latitude', 20, 16 );
            $table->decimal( 'price' );
            $table->string( 'unit' );
            $table->text( 'description' );
            $table->string( 'state' );
            $table->bigInteger( 'city_id' );
            $table->bigInteger( 'district_id' );
            $table->float( 'area' );
            $table->integer( 'rooms' )->default( 1 );
            $table->integer( 'floor' )->default( 1 );
            $table->integer( 'floor_max' )->default( 1 );
            $table->bigInteger( 'user_id' );
            $table->timestamps();

            $table->index( [ 'city_id', 'type', 'state' ] );
            $table->index( [ 'district_id', 'type', 'state' ] );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'offers' );
    }
}
