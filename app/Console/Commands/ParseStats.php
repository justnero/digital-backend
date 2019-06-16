<?php

namespace App\Console\Commands;

use App\District;
use App\Offer;
use App\Statistic;
use Illuminate\Console\Command;
use League\Geotools\Coordinate\Coordinate;
use Storage;

class ParseStats extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:parse-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse stat data';

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
        $data      = Storage::disk( 'local' )->get( 'districts.csv' );
        $lines     = array_map( 'trim', explode( "\n", $data ) );
        $header    = array_map( 'trim', str_getcsv( $lines[0], ';' ) );
        $header[0] = 'id';

        $lines = array_slice( $lines, 1 );
        foreach ( $lines as $line ) {
            $row = array_map( 'trim', str_getcsv( $line, ';' ) );
            if ( count( $row ) < 4 ) {
                continue;
            }
            $row = collect( array_combine( $header, $row ) );

            foreach ( $row->except( [ 'id', 'cat', 'price' ] ) as $key => $value ) {
                $value = (float) rtrim( $value, '%' );

                $attr = [
                    'statable_id'   => $row['id'],
                    'statable_type' => 'district',
                    'key'           => $key,
                ];
                $stat = Statistic::where( $attr )->firstOrNew( $attr );

                $stat->value = $value;
                $stat->save();
            }
        }
    }
}
