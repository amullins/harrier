<?php


/**
 * Class PathSegment
 */
final class PathSegment {
    /** @var string The name of the PathSegment. This name will be used for storing data to Location::$data */
    public $name = '';

    /**
     * @param string $name @see PathSegment::$name
     */
    public function PathSegment($name) {
        $this->name = $name;
    }
}