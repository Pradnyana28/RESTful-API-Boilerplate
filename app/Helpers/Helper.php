<?php

namespace App\Helpers;

class Helper {
    public static function timeFormat($time) {
        list($hours, $minutes) = explode(':', $time);
        return $hours >= 12 ? $hours .':'. $minutes .' PM' : $hours .':'. $minutes .' AM';
    }

    public static function instance() {
        return new AppHelper();
    }
}