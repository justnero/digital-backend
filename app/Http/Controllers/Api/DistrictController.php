<?php

namespace App\Http\Controllers\Api;

use App\District;
use App\Http\Controllers\Controller;

class DistrictController extends Controller {
    public function index() {
        return District::with( 'statistics' )->get();
    }

    public function show( District $district ) {
        return $district;
    }
}
