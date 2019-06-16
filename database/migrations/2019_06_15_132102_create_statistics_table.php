<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatisticsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'statistics', function ( Blueprint $table ) {
            $table->bigIncrements( 'id' );
            $table->morphs( 'statable' );
            $table->string( 'key' );
            $table->float( 'value' );
            $table->timestamps();

            $table->index( [ 'statable_id', 'statable_type', 'key' ] );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'statistics' );
    }
}
