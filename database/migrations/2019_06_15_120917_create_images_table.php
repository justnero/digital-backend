<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'images', function ( Blueprint $table ) {
            $table->bigIncrements( 'id' );
            $table->bigInteger( 'offer_id' );
            $table->string( 'disk' );
            $table->string( 'path' );
            $table->integer( 'order' );
            $table->timestamps();

            $table->index(['offer_id', 'order']);
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'images' );
    }
}
