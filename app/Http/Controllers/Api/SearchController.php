<?php

namespace App\Http\Controllers\Api;

use App\District;
use App\Http\Controllers\Controller;
use App\Offer;
use Illuminate\Http\Request;

class SearchController extends Controller {
    public function offer( Request $request ) {
        $orm = Offer::query();
        $q   = collect( $request->only( [ 'district', 'type', 'area', 'price', 'rooms' ] ) )
            ->filter( function ( $value ) {
                return $value && $value !== 'null';
            } );

        foreach ( $q as $key => $value ) {
            $key = $this->mapKey( $key );
            if ( $key === 'district_id' ) {
                $orm->whereIn( 'district_id', explode( ',', $value ) );
            } else if ( is_array( $value ) ) {
                $orm->whereBetween( $key, $value );
            } else if ( strpos( $value, ',' ) !== false ) {
                $orm->whereBetween( $key, explode( ',', $value ) );
            } else {
                $orm->where( $key, $value );
            }
        }

        $rows = $orm->get();

        $stats = array_filter( explode( ',', $request->get( 'stats', '' ) ) );
        foreach ( $stats as $stat ) {
            $rows = $rows->filter( function ( Offer $offer ) use ( $stat ) {
                foreach ( $this->mapStat( $stat ) as $s ) {
                    if ( ( $offer->stats[ $s ] ?? 0 ) >= 1 ) {
                        return true;
                    }
                }

                return false;
            } );
        }

        return $rows;
    }

    private function mapKey( string $key ): string {
        switch ( $key ) {
            case 'city':
                return 'city_id';
            case 'district':
                return 'district_id';
            default:
                return $key;
        }
    }

    private function mapStat( $stat ) {
        switch ( $stat ) {
            case 'cafe':
                return [ 'bar', 'restaurant', 'cafe', 'fast_food', 'pub' ];
            case 'market':
                return [ 'marketplace' ];
            case 'bus':
                return [ 'bus_stop' ];
            case 'hospital':
                return [ 'clinic', 'hospital', 'dentist', 'veterinary', 'doctors' ];
            case 'school':
                return [ 'college', 'kindergarten', 'school' ];
            default:
                return [ $stat ];
        }
    }

    public function district( Request $request ) {
        $multiplier = $request->get( 'type','buy' ) === 'buy' ? 1 : - 1;
        $stats      = array_filter( explode( ',', $request->get( 'stats', '' ) ) );
        $districts  = District::all()->map( function ( District $district ) use ( $stats, $multiplier ) {
            $district->score = collect( $district->stats )
                                   ->only( $stats )
                                   ->sum() * $multiplier;

            return $district;
        } )->sortByDesc( 'score' );

        return array_values($districts->toArray());
    }
}
