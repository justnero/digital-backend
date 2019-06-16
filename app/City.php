<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\City
 *
 * @property int $id
 * @property string $name
 * @property float $longitude
 * @property float $latitude
 * @property int $osm_id
 * @property string $boundary
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\District[] $districts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Offer[] $offers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\City query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\City whereBoundary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\City whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\City whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\City whereOsmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\City whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class City extends Model {
    protected $fillable = [
        'name',
        'longitude',
        'latitude',
        'osm_id',
        'boundary',
    ];

    public function districts() {
        return $this->hasMany( District::class );
    }

    public function offers() {
        return $this->hasMany( Offer::class );
    }
}
