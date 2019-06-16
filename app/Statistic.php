<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Statistic
 *
 * @property int $id
 * @property int $district_id
 * @property string $key
 * @property float $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\District $district
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Statistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Statistic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Statistic query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Statistic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Statistic whereDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Statistic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Statistic whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Statistic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Statistic whereValue($value)
 * @mixin \Eloquent
 */
class Statistic extends Model {
    public const TYPES = [

    ];

    protected $fillable = [
        'statable_id',
        'statable_type',
        'key',
        'value',
    ];

    public function statable() {
        return $this->morphTo();
    }
}
