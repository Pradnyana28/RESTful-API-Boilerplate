<?php

use Illuminate\Http\Request;

// NOTE:: prefix is /v1

Route::group([
    'middleware' => 'api'
], function($router) {
    // your route goes here
});