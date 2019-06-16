<?php

namespace App\Console\Commands;

use App\District;
use App\POI;
use Illuminate\Console\Command;
use KageNoNeko\OSM\OverpassConnection;
use KageNoNeko\OSM\Query\OverpassBuilder;

class ParseDistrict extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osm:parse-district';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $osm = new OverpassConnection( [ 'interpreter' => 'https://overpass-api.de/api/interpreter' ] );
        $this->loadPOI( $osm->element( 'node' )->whereTagExists( 'amenity' ) );
        $this->loadPOI( $osm->element( 'node' )->whereTag( 'highway', 'bus_stop' ) );
    }

    protected function loadPOI( OverpassBuilder $q ) {
        $q = $q->whereInBBox( 44.512094, 33.3779569, 44.7221413, 33.6103428 )
               ->asJson();

        $response = $q->get();

        $data = collect( json_decode( $response->getBody()->getContents(), true )['elements'] )
            ->filter( function ( $el ) {
                return $el['type'] === 'node';
            } )
            ->map( function ( $el ) {
                return [
                    'id'   => $el['id'],
                    'lon'  => $el['lon'],
                    'lat'  => $el['lat'],
                    'type' => $el['tags']['amenity'] ?? $el['tags']['highway'],
                    'name' => $el['tags']['name:ru'] ?? $el['tags']['name'] ?? '',
                ];
            } );

        $this->output->progressStart( $data->count() );
        $this->setProcessTitle( 'Загрузка POI' );
        $data->each( function ( $poiData ) {
            $this->maybeCreatePOI( $poiData );
            $this->output->progressAdvance();
        } );
        $this->output->progressFinish();
    }

    private function maybeCreatePOI( $poiData ) {
        $poi = POI::where( [ 'osm_id' => $poiData['id'] ] )
                  ->firstOrNew( [
                      'osm_id' => $poiData['id'],
                  ] );

        $poi->name      = $poiData['name'];
        $poi->type      = $poiData['type'];
        $poi->longitude = $poiData['lon'];
        $poi->latitude  = $poiData['lat'];
        $poi->save();
    }
}
