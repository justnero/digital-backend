<?php

namespace App\Console\Commands;

use App\City;
use Illuminate\Console\Command;
use Services_OpenStreetMap;
use Storage;

class LoadCity extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osm:load-city';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load city OSM map';

    /** @var Services_OpenStreetMap */
    protected $osm;

    /** @var string */
    protected $city;

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
        $this->city = 'Севастополь';

        $attrs = $this->osm->getPlace( $this->city )[0]->attributes();

        $boundingBox = (string) $attrs['boundingbox'];
        dump($boundingBox);
        $bbox        = array_map( 'floatval', explode( ',', $boundingBox ) );

        $box = [
            'lon' => [ $bbox[2], $bbox[3] ],
            'lat' => [ $bbox[0], $bbox[1] ],
        ];
        $this->info( "City: {$this->city} Bounding box: {$boundingBox}" );
        $this->maybeCreate( $attrs );

//        if ( $this->askWithCompletion( 'Load osm?', [ 'yes', 'no' ], 'yes' ) === 'yes' ) {
//            $this->loadRegion( $box );
//        }
    }

    public function maybeCreate( $attrs ): void {
        $city = City::whereName( $this->city )->firstOrNew( [ 'name' => $this->city ] );

        $city->boundary  = (string) $attrs['boundingbox'];
        $city->longitude = (float) ( (string) $attrs['lon'] );
        $city->latitude  = (float) ( (string) $attrs['lat'] );
        $city->osm_id    = (int) ( (string) $attrs['osm_id'] );

        $city->save();
    }

    public function loadRegion( array $box ): void {
        $size = 8;
        $this->output->progressStart( $size * $size );
        for ( $i = 0; $i < $size * $size; $i ++ ) {
            $this->loadChunk( $box, [
                'lon' => $size,
                'lat' => $size,
            ], [
                'lat' => $i / $size,
                'lon' => $i % $size,
            ],
                $i, $size );
            $this->output->progressAdvance();
        }
    }

    protected function loadChunk( array $bbox, array $chunks, array $index, string $id, string $size ): void {
        $lon = [ 0, 0 ];
        $lat = [ 0, 0 ];
        extract( $bbox );
        $lonChunk = ( $lon[1] - $lon[0] ) / (float) $chunks['lon'];
        $latChunk = ( $lat[1] - $lat[0] ) / (float) $chunks['lat'];
        $box      = [
            'lon' => [
                $lon[0] + $lonChunk * ( $index['lon'] + 0 ),
                $lon[0] + $lonChunk * ( $index['lon'] + 1 ),
            ],
            'lat' => [
                $lat[0] + $latChunk * ( $index['lat'] + 0 ),
                $lat[0] + $latChunk * ( $index['lat'] + 1 ),
            ],
        ];

        $this->osm->get( $box['lon'][0], $box['lat'][0], $box['lon'][1], $box['lat'][1] );
        Storage::disk( "osm" )->put( "{$this->city}/{$size}x{$size}/{$id}.osm", $this->osm->getXml() );
    }
}
