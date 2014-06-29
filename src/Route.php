<?php


/**
 * Class Route
 */
final class Route {
    /** @var array Array representation of a URL path. */
    public $path = null;

    /** @var array Array representation of a template path. */
    public $template = null;

    /** @var bool Does the path have dynamic segments? */
    public $dynamic = false;

    /** @var string A regular expression for matching dynamic Routes. */
    public $regex = null;

    /**
     * @param array $path @see Route::$path
     * @param array $template @see Route::$template
     */
    public function Route($path, $template) {
        // find segments of path that contain data to be extracted upon visiting
        $this->path = array_map(function ($segment) {
            if (Utilities::starts_with($segment, ':')) {
                $this->dynamic = true;
                return new PathSegment(substr($segment, 1));
            }
            return $segment;
        }, $path);

        $this->template = $template;

        if ($this->dynamic) $this->setRegex();
    }

    /**
     * @param array $path @see Route::$path
     * @param array $template @see Route::$template
     * @return Route
     */
    public static function define($path, $template) {
        return new Route($path, $template);
    }

    /**
     * Set this Route's regular expression (only set for dynamic Routes).
     */
    private function setRegex() {
        $regex = '/^(';

        $temp = array_map(function($segment) {
            if (is_string($segment)) return $segment;
            return '[^\/]*';
        }, $this->path);

        $regex .= implode('\/', $temp) . ')$/';

        $this->regex = $regex;
    }
}