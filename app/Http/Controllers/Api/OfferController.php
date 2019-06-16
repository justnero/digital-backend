<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller {
    public function show( Offer $offer ) {
        return $offer;
    }

    public function book( Request $request, Offer $offer ) {
        return $offer;
    }

    public function buy( Request $request, Offer $offer ) {
        return $offer;
    }
}
