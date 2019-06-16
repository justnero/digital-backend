<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use League\Geotools\Polygon\Polygon;

/**
 * App\District
 *
 * @property int $id
 * @property string $name
 * @property int $city_id
 * @property int $osm_id
 * @property string $boundary
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\City $city
 * @property-read mixed $offer_count
 * @property-read Polygon $polygon
 * @property-read mixed $stats
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Offer[] $offers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Statistic[] $statistics
 * @method static \Illuminate\Database\Eloquent\Builder|\App\District newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\District newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\District query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\District whereBoundary( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\District whereCityId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\District whereCreatedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\District whereId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\District whereName( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\District whereOsmId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\District whereUpdatedAt( $value )
 * @mixin \Eloquent
 */
class District extends Model {
    protected $fillable = [
        'name',
        'city_id',
        'longitude',
        'latitude',
        'osm_id',
        'boundary',
    ];

    protected $appends = [
        'offer_count',
        'stats',
    ];

    protected $hidden = [
        'statistics',
    ];

    public function city() {
        return $this->belongsTo( City::class );
    }

    public function offers() {
        return $this->hasMany( Offer::class );
    }

    public function statistics() {
        return $this->morphMany( Statistic::class, 'statable' );
    }

    public function getOfferCountAttribute() {
        return $this->offers()->count();
    }

    public function getStatsAttribute() {
        return $this->statistics->mapWithKeys( function ( Statistic $stat ) {
            return [ $stat->key => $stat->value ];
        } );
    }

    /**
     * @return Polygon
     */
    public function getPolygonAttribute() {
        $boundary = array_map( function ( $boundary ) {
            return [ $boundary[1], $boundary[0] ];
        }, json_decode( $this->boundary, true ) );

        return new Polygon( $boundary );
    }
}
