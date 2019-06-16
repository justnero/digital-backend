<?php

namespace App\Console\Commands;

use App\District;
use App\POI;
use App\Statistic;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ParsePOIs extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osm:parse-poi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse POIs';

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
        $stats     = [];
        $pois      = POI::all();
        $this->output->progressStart( $pois->count() );
        $warn = [];
        $pois->each( function ( POI $poi ) use ( $districts, &$stats, &$warn ) {
            $district = $this->parsePOI( $poi, $districts );
            if ( $district ) {
                if ( ! isset( $stats[ $district->id ] ) ) {
                    $stats[ $district->id ] = [];
                }
                if ( ! isset( $stats[ $district->id ][ $poi->type ] ) ) {
                    $stats[ $district->id ][ $poi->type ] = 0;
                }
                $stats[ $district->id ][ $poi->type ] ++;
            } else {
                $warn[] = $poi;
            }
            $this->output->progressAdvance();
        } );
        $this->output->progressFinish();
        foreach ( $warn as $poi ) {
            $this->warn( "{$poi->id} is not in any district" );
        }
        foreach ( $stats as $districtId => $dStats ) {
            foreach ( $dStats as $type => $count ) {
                $attrs = [
                    'statable_type' => 'district',
                    'statable_id'   => $districtId,
                    'key'           => $type,
                ];
                $stat  = Statistic::where( $attrs )
                                  ->firstOrNew( $attrs );

                $stat->value = $count;
                $stat->save();
            }
        }
    }

    /**
     * @param POI $poi
     * @param District[]|Collection $districts
     *
     * @return District|null
     */
    private function parsePOI( POI $poi, Collection $districts ): ?District {
        foreach ( $districts as $district ) {
            if ( $district->polygon->pointInPolygon( $poi->coordinate ) ) {
                return $district;
            }
        }

        return null;
    }
}
