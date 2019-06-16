<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use League\Geotools\Coordinate\Coordinate;

/**
 * App\POI
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property float $longitude
 * @property float $latitude
 * @property int $osm_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Coordinate $coordinate
 * @method static \Illuminate\Database\Eloquent\Builder|\App\POI newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\POI newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\POI query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\POI whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\POI whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\POI whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\POI whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\POI whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\POI whereOsmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\POI whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\POI whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class POI extends Model {
    protected $table = 'pois';

    protected $fillable = [
        'type',
        'name',
        'longitude',
        'latitude',
        'osm_id',
    ];

    /**
     * @return Coordinate
     */
    public function getCoordinateAttribute() {
        return new Coordinate( [ $this->latitude, $this->longitude ] );
    }
}
