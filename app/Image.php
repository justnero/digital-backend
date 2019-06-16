<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Image
 *
 * @property int $id
 * @property int $offer_id
 * @property string $disk
 * @property string $path
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Offer $offer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereCreatedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereDisk( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereOfferId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereOrder( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image wherePath( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereUpdatedAt( $value )
 * @mixin \Eloquent
 */
class Image extends Model {
    protected $fillable = [
        'offer_id',
        'disk',
        'path',
        'order',
    ];

    protected $appends = [
        'url',
    ];

    protected $hidden = [
        'id',
        'offer_id',
        'disk',
        'path',
        'created_at',
        'updated_at',
    ];

    public function offer() {
        return $this->belongsTo( Offer::class );
    }

    public function getUrlAttribute() {
        return \Storage::disk( $this->disk )->url( $this->path );
    }
}
