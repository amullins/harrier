<?php

include 'Location.php';
include 'Request.php';


/**
 * Contains all Locations for app.
 * Class Sitemap
 */
final class Sitemap {
    /** The current Request object. */
    public $request = null;

    /** All Locations (except ErrorLocations) */
    private $entries = null;

    /** All ErrorLocations */
    private $errorPages = array();

    /**
     * Varargs. Can be a single array of Locations or multiple Locations specified as individual arguments.
     */
    public function Sitemap() {
        if (func_num_args() == 1) $this->append(func_get_arg(0));
        else $this->append(func_get_args());
    }

    /**
     * Add new Location(s) to the sitemap.
     * @param mixed $entry An array of Locations or a single Location.
     */
    public function append($entry) {
        if (is_array($entry))
            foreach ($entry as $loc) $this->append($loc);

        else if (get_class($entry) === 'Location' || is_subclass_of($entry, 'Location')) {
            if (get_class($entry) === 'ErrorLocation') $this->errorPages[$entry->code] = $entry;
            else $this->entries[] = $entry;
        }
    }

    /**
     * Go to the matched Location (based on current Request).
     */
    public function go() {
        $this->request = new Request($_SERVER['REQUEST_URI']);

        foreach ($this->entries as $entry) {
            if ($entry->matches($this->request->path)) {
                App::location($entry);
                break;
            }
        }

        if (!is_null(App::location())) {
            App::location()->init();
            App::location()->process();
        } else {
            $this->errorPages[404]->process();
        }
    }

    /**
     * Find a location by name.
     * @param string $name The name of the location to be retrieved.
     * @return null|Location
     */
    public static function location($name) {
        foreach (App::sitemap()->entries as $entry) {
            if ($entry->matchesName($name)) return $entry;
        }
        return null;
    }
}
