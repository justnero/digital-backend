<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Geotools;

/**
 * App\Offer
 *
 * @property int $id
 * @property string $type
 * @property string $address
 * @property float $longitude
 * @property float $latitude
 * @property float $price
 * @property string $unit
 * @property string $description
 * @property string $state
 * @property int $city_id
 * @property int $district_id
 * @property float $area
 * @property int $floor
 * @property int $floor_max
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\City $city
 * @property-read \App\District $district
 * @property-read Coordinate $coordinate
 * @property-read mixed $embedded
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Image[] $images
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereAddress( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereArea( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereCityId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereCreatedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereDescription( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereDistrictId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereFloor( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereFloorMax( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereLatitude( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereLongitude( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer wherePrice( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereState( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereType( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereUnit( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereUpdatedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereUserId( $value )
 * @mixin \Eloquent
 */
class Offer extends Model {
    public const TYPE_SELL = 'sell';
    public const TYPE_RENT = 'rent';

    public const STATE_NEW = 'new';
    public const STATE_READY = 'published';
    public const STATE_CLOSED = 'closed';

    protected $fillable = [
        'type',
        'address',
        'longitude',
        'latitude',
        'price',
        'unit',
        'description',
        'state',
        'city_id',
        'district_id',
        'area',
        'floor',
        'floor_max',
        'rooms',
        'user_id',
    ];

    protected $appends = [
        'stats',
        'embedded',
    ];

    protected $hidden = [
        'statistics',
    ];

    public function city() {
        return $this->belongsTo( City::class );
    }

    public function district() {
        return $this->belongsTo( District::class );
    }

    public function images() {
        return $this->hasMany( Image::class )->orderBy( 'order' );
    }

    public function statistics() {
        return $this->morphMany( Statistic::class, 'statable' );
    }

    public function getStatsAttribute() {
        return $this->statistics->mapWithKeys( function ( Statistic $stat ) {
            return [ $stat->key => $stat->value ];
        } );
    }

    public function getEmbeddedAttribute() {
        return [
            'city'     => $this->city,
            'district' => $this->district,
            'images'   => $this->images,
        ];
    }

    /**
     * @return Coordinate
     */
    public function getCoordinateAttribute() {
        return new Coordinate( [ $this->latitude, $this->longitude ] );
    }

    public function getPoiStatsAttribute() {
        $pois       = POI::all();
        $coordinate = $this->coordinate;
        $geotools   = ( new Geotools() )->distance()->setFrom( $coordinate )->in( 'km' );
        $stats      = [];
        foreach ( $pois as $poi ) {
            $distance = $geotools->setTo( $poi->coordinate )->vincenty();
            if ( $distance <= 5 ) {
                $distance = $distance / 5.0;
                $stat     = min( 1, 1 - $distance + 0.2 );
                if ( ! isset( $stats[ $poi->type ] ) ) {
                    $stats[ $poi->type ] = 0;
                }
                $stats[ $poi->type ] += $stat;
            }
        }

        return $stats;
    }
}
