<?php

include 'Sitemap.php';
include 'Utilities.php';
include 'Snippet.php';
include 'Twig.php';
include 'Data.php';

// Twig config
const TEMPLATE_CACHE = false;
const TEMPLATE_CACHE_PATH = 'templatecache';
const TEMPLATE_BASE_DIR = 'template';


/**
 * Class App
 */
final class App {
    /** @var Sitemap The application Sitemap. */
    public $sitemap = null;

    /** @var array All application Snippets. */
    public $snippets = array();

    /** @var Location The current Location. */
    private $location = null;

    /** @var Twig_Environment */
    private $twig = null;

    /** @var array All application data. */
    private $data = array();

    /** @var Emailer */
    private $emailer = null;

    private function App() {}

    public static function init($options = array(), $twig_globals = array()) {
        $GLOBALS['app'] = new App();

        session_start();

        spl_autoload_register(array(self::instance(), 'autoload'));

        require_once __DIR__ . '/../vendor/autoload.php';

        if (isset($options['mail-username'])) {
            self::instance()->emailer = new Emailer($options['mail-username'], $options['mail-password']);
        }

        TwigConfig::init(TEMPLATE_CACHE, TEMPLATE_CACHE_PATH, TEMPLATE_BASE_DIR, $twig_globals);
    }

    public function autoload($clazz) {
        $directories = array(
            'src',
            'snippet',
            'model',
            'model/data'
        );

        foreach ($directories as $dir) {
            $filePath = __DIR__ . '/../' . $dir . '/' . $clazz . '.php';
            if (file_exists($filePath)) {
                require_once($filePath);
                return;
            }
        }
    }

    /**
     * Get this application instance.
     * @return App
     */
    private static function instance() {
        return $GLOBALS['app'];
    }

    /**
     * Used for setting AND retrieving the App's Sitemap.
     * @param mixed $sitemap Sitemap object (could also be a single Location if using the vararg capabilities of this method).
     * @return Sitemap
     *
     * Can supply multiple Locations as a variable length argument list.
     */
    public static function sitemap($sitemap = null) {
        $a = self::instance();

        if (is_subclass_of($sitemap, 'Sitemap'))
            $a->sitemap = $sitemap;

        else if (is_null($a->sitemap))
            $a->sitemap = new Sitemap(func_get_args());

        else if (!is_null($sitemap))
            $a->sitemap->append(func_get_args());

        return $a->sitemap;
    }

    /**
     * @param string $key The key under which this data should be stored.
     * @param array|Data $data The data to append.
     * @return array The current data for the app.
     */
    public static function store($key, $data) {
        if (is_array($data))
            self::instance()->data[$key] = Data::init($data);

        else if (is_object($data) && (get_class($data) == 'Data' || is_subclass_of($data, 'Data')))
            self::instance()->data[$key] = $data;

        return self::instance()->data;
    }

    /**
     * @param $key
     */
    public static function unstore($key) {
        unset(self::instance()->data[$key]);
    }

    /**
     * @param string $key The data your wish to retrieve.
     * @return Data
     */
    public static function data($key) {
        return self::instance()->data[$key];
    }

    /**
     * Used for setting AND retrieving App snippets.
     * @param array $arr Associative array.
     * @return array
     */
    public static function snippets($arr = null) {
        $a = self::instance();

        if (!is_null($arr))
            $a->snippets = array_merge($a->snippets, $arr);

        return $a->snippets;
    }

    /**
     * Retrieve a new Snippet instance by name.
     * @param string $name Snippet name.
     * @return null|Snippet
     */
    public static function getSnippetInstance($name) {
        $snips = self::instance()->snippets();
        if (array_key_exists($name, $snips)) return new $snips[$name]($name);
        return null;
    }

    /**
     * Used for setting AND retrieving the App's current Location.
     * @param Location $loc The current Location.
     * @return Location
     */
    public static function location($loc = null) {
        $a = self::instance();

        if (!is_null($loc))
            $a->location = $loc;

        return $a->location;
    }

    /**
     * Get the Twig Environment for use with rendering templates.
     * @param Twig_Environment $env
     * @return Twig_Environment
     */
    public static function twig($env = null) {
        if (!is_null($env)) self::instance()->twig = $env;

        return self::instance()->twig;
    }

    /**
     * Process a new request.
     */
    public static function processRequest() {
        App::sitemap()->go();
    }

    /**
     * @param string $to Email address.
     * @param string $from Email address.
     * @param string $subject Email subject.
     * @param string $html Email body.
     * @return object
     */
    public static function mail($to, $from, $subject, $html) {
        $result = self::instance()->emailer->send($to, $from, $subject, $html);

        return json_decode($result);
    }
}
