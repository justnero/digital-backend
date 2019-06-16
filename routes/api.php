<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware( 'auth:api' )->get( '/user', function ( Request $request ) {
    return $request->user();
} );

Route::get( '/q/offer', 'SearchController@offer' );
Route::get( '/q/district', 'SearchController@district' );

Route::get( '/offer/{offer}', 'OfferController@show' );
Route::post( '/offer/{offer}/book', 'OfferController@book' );

Route::get( '/district/', 'DistrictController@index' );
Route::get( '/district/{district}', 'DistrictController@show' );
