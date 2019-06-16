<?php

namespace App\Console\Commands;

use App\District;
use App\Offer;
use Illuminate\Console\Command;
use League\Geotools\Coordinate\Coordinate;
use Storage;

class LoadOffers extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:load-offers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load offers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle() {
        $districts = District::all();

        $data  = Storage::disk( 'local' )->get( 'offers.csv' );
        $lines = array_map( 'trim', explode( "\n", $data ) );
        foreach ( $lines as $line ) {
            $row = array_map( 'trim', str_getcsv( $line, ';' ) );
            if(count($row) < 4) {
                continue;
            }

            $row[3] = str_replace( ',', '.', $row[3] );
            $row[4] = str_replace( ',', '.', $row[4] );
            $row[8] = str_replace( ',', '.', $row[8] );


            $coordinate  = new Coordinate( [ $row[3], $row[4] ] );
            $district_id = - 1;
            foreach ( $districts as $district ) {
                if ( $district->polygon->pointInPolygon( $coordinate ) ) {
                    $district_id = $district->id;
                    break;
                }
            }

            Offer::create( [
                'type'        => $row[1] === 'п' ? Offer::TYPE_SELL : Offer::TYPE_RENT,
                'address'     => $row[2],
                'longitude'   => $row[4],
                'latitude'    => $row[3],
                'price'       => filter_var( $row[5], FILTER_SANITIZE_NUMBER_FLOAT ),
                'unit'        => $row[6] === 'с' ? 'day' : ( $row[6] === 'м' ? 'month' : '' ),
                'description' => $row[7],
                'area'        => $row[8],
                'floor'       => $row[9],
                'rooms'       => $row[10],
                'floor_max'   => $row[11],
                'city_id'     => 1,
                'district_id' => $district_id,
                'state'       => Offer::STATE_NEW,
                'user_id'     => 0,
            ] );
        }
    }
}
