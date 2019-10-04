<?php
namespace Container;

class Container {
    private static $dependencies = [];

    public static function set(String $key, $func) {
        self::$dependencies[$key] = $func;
    }

    public static function get(String $key) {
        return self::$dependencies[$key]();
    }
}