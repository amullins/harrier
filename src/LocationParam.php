<?php


/**
 * Class LocationParam
 * A param to be applied to a Location instance.
 * A param may also be applied globally (for all new instances) via LocationParam::defaults
 */
class LocationParam {
    /** Holds the value of this param. */
    public $value = null;

    /** Is this a global param (new params of same type will inherit from this param). */
    private $global = false;

    /** Contains default params (some of which may be global). */
    private static $defaultParams = array();

    /**
     * @param mixed $value The param's value.
     * @param bool $default Is this a default param?
     * @param bool $global Is this a global param?
     */
    private function LocationParam($value, $default = false, $global = false) {
        // a param must be declared as a default param to be a global param
        if ($default) $this->global = $global;

        // inheritance for non-default params
        if (!$default && is_array($value))
            $this->value = array_merge(LocationParam::$defaultParams[get_called_class()]->value, $value);

        else
            $this->value = $value;
    }

    /**
     * @return bool True if this is a global param.
     */
    public function isGlobal() {
        return $this->global;
    }

    /**
     * @param mixed $value The param's value.
     * @param bool $default Is this a default param?
     * @param bool $global Is this a global param?
     * @return mixed A new LocationParam or Subclass thereof
     */
    public static function define($value, $default = false, $global = false) {
        $clazz = get_called_class();
        return new $clazz($value, $default, $global);
    }

    /**
     * Define default params through this static method.
     * LocationParam::defaults(new Meta(...)...)
     */
    public static function defaults() {
        foreach (func_get_args() as $defaultParam) {
            self::$defaultParams[get_class($defaultParam)] = $defaultParam;
        }
    }

    /**
     * Get global params.
     * @return array All global params.
     */
    public static function globals() {
        return array_filter(self::$defaultParams, function($param) {
            return $param->isGlobal();
        });
    }
}