<?php


/**
 * Class Location
 */
class Location {
    /** @var string The Location name (should be unique across all Sitemap entries - not enforced). */
    public $name = '';

    /** @var mixed The Location title (used in HTML title). This can be a static string of a closure that returns a string. */
    public $title = null;

    /** @var Route The Route for the Location (determines what URL to match and what template to be display). */
    public $route = null;

    /** @var array LocationParams for this Location. */
    public $params = null;

    /** @var array Data from URL. */
    public $data = array();

    /** @var array Meta params for Location. */
    public $meta = array();

    /**
     * @param string $name @see Location::$name
     * @param mixed $title @see Location::$title
     * @param Route $route @see Location::$route
     *
     * @throws Exception
     *
     * Append variable number of LocationParams to end of argument list.
     */
    public function Location($name, $title, $route) {
        $this->title = $title;
        $this->name = $name;
        $this->route = $route;

        $this->setParams(array_slice((func_get_args()), 3));
    }

    /**
     * @param string $name @see Location::$name
     * @param mixed $title @see Location::$title
     * @param Route $route @see Location::$route
     * @return Location
     *
     * Append variable number of LocationParams to end of argument list.
     */
    public static function create($name, $title, $route) {
        return new Location($name, $title, $route, array_slice((func_get_args()), 3));
    }

    /**
     * Add LocationParams to Location (inherit from global default params).
     * @param array $params LocationParams to append to Location.
     */
    private function setParams($params) {
        $this->params = LocationParam::globals();

        if (count($params)) {
            $instanceParams = is_array($params[0]) ? $params[0] : $params;

            foreach ($this->params as $k => $globalParam) {
                foreach ($instanceParams as $param) {
                    if (get_class($param) == get_class($globalParam))
                        unset($this->params[$k]);
                }
            }

            $this->params = array_merge($this->params, $instanceParams);
        }

        foreach ($this->params as $k => $param) {
            if (is_object($param) && get_class($param) == 'Meta') {
                $this->meta = $param->value;
                unset($this->params[$k]);
            }
        }
    }

    /**
     * @param mixed $param What to match. Can be an array representation of path, a Location name, a Request or another Location.
     * @return bool|int
     */
    public function matches($param) {
        if (is_array($param))
            return $this->matchesPath($param);

        else if (is_string($param))
            return $this->matchesName($param);

        else
            switch (get_class($param)) {
                case 'Location' :
                    return $this->matchesName($param->name);
                    break;
                case 'Request' :
                    return $this->matchesPath($param->path);
                    break;
                default :
                    if (is_object($param) && is_subclass_of($param, 'Location'))
                        return $this->matchesName($param->name);
            }

        return false;
    }

    /**
     * @param array $path The array representation of a path to match.
     * @return bool|int
     */
    public function matchesPath(array $path) {
        if ($this->route->dynamic)
            return preg_match($this->route->regex, implode('/', $path));

        return $path == $this->getPath();
    }

    /**
     * @param string $name The Location name to match.
     * @return bool
     */
    public function matchesName($name) {
        return $this->name == $name;
    }

    /**
     * Get the array representation of this this Location's path (the Route's path).
     * @return array|null
     */
    public function getPath() {
        return $this->route->path;
    }

    /**
     * Get the URL of this Location.
     * @param bool $absolute Should the URL be made absolute.
     * @return string
     */
    public function getUrl($absolute = false) {
        return $absolute
            ? Utilities::makeAbsoluteUrl('/' . implode('/', $this->route->path))
            : ('/' . implode('/', $this->route->path));
    }

    /**
     * Get the array representation of this Location's template (the Route's template).
     * @return array|null
     */
    public function getTemplate() {
        return $this->route->template;
    }

    /**
     * Get this Location's title.
     * @return string
     */
    public function getTitle() {
        if (is_string($this->title))
            return $this->title;

        else if (is_callable($this->title))
            return $this->title->__invoke($this->data);

        return 'undefined';
    }

    /**
     * Retrieves data from URL and saves it to this Location's data array.
     */
    public function init() {
        if ($this->route->dynamic) {
            foreach ($this->route->path as $index => $segment) {
                if (is_object($segment) && get_class($segment) == 'PathSegment')
                    $this->data[$segment->name] = App::sitemap()->request->path[$index];
            }
        }
    }

    /**
     * Display this Location. By default, this will render the Twig template as defined under this Location's Route.
     */
    public function process() {
        echo App::twig()->render(
            implode('/', $this->route->template),
            array(
                'location' => $this,
                'page_title' => $this->getTitle()
            )
        );
    }
}