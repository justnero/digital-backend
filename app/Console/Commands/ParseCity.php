<?php

namespace App\Console\Commands;

use App\District;
use Illuminate\Console\Command;
use KageNoNeko\OSM\OverpassConnection;
use Services_OpenStreetMap;

class ParseCity extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osm:parse-city';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse city data';

    /** @var Services_OpenStreetMap */
    protected $osm;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->osm = new Services_OpenStreetMap();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void {
        $this->loadDistricts();
    }

    protected function loadDistricts() {
        $osm      = new OverpassConnection( [ 'interpreter' => 'https://overpass-api.de/api/interpreter' ] );
        $q        = $osm->element( 'rel' )
                        ->whereTag( 'boundary', 'administrative' )
                        ->whereTag( 'admin_level', '8' )
                        ->whereInBBox( 44.512094, 33.3779569, 44.7221413, 33.6103428 )
                        ->asJson();
        $response = $q->get();

        $data = collect( json_decode( $response->getBody()->getContents(), true )['elements'] )
            ->filter( function ( $el ) {
                return $el['type'] === 'relation';
            } )
            ->map( function ( $el ) {
                return [
                    'id'       => $el['id'],
                    'name'     => $el['tags']['name'],
                    'boundary' => $this->getBoundary( $el['id'] ),
                ];
            } );

        $this->output->progressStart( $data->count() );
        $this->setProcessTitle( 'Загрузка полигонов округов' );
        $data->each( function ( $districtData ) {
            $this->maybeCreateDistrict( $districtData );
            $this->output->progressAdvance();
        } );
        $this->output->progressFinish();
    }

    protected function getBoundary( int $id ) {
        $data = json_decode( file_get_contents( "http://polygons.openstreetmap.fr/get_geojson.py?id={$id}&params=0" ), true );

        return $data['geometries'][0]['coordinates'][0][0];
    }

    protected function maybeCreateDistrict( $districtData ) {
        $district = District::where( [ 'name' => $districtData['name'] ] )
                            ->orWhere( [ 'osm_id' => $districtData['id'] ] )
                            ->firstOrNew( [
                                'name'    => $districtData['name'],
                                'osm_id'  => $districtData['id'],
                                'city_id' => 1,
                            ] );

        $district->boundary = json_encode( $districtData['boundary'] );
        $district->save();
    }
}
