<?php


/**
 * Class ArrayLocationParam
 * A param whose value is an array.
 */
class ArrayLocationParam extends LocationParam {
    public function extend($value) {
        $this->value = $this->value + $value;

        return $this;
    }
}