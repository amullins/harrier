<?php

require_once __DIR__. '/../vendor/twig/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();


class TwigConfig {
    /** @var Twig_Loader_Filesystem */
    private $loader = null;

    /** @var array */
    private $config = array();

    private function TwigConfig($cache, $cachePath, $baseDir, $extraGlobals) {
        $this->loader = new Twig_Loader_Filesystem(__DIR__ . '/../' . $baseDir);

        if ($cache) $this->config['cache'] = $cachePath;

        $twigEnvironment = new Twig_Environment($this->loader, $this->config);
        $twigEnvironment->addExtension(new SimpleSiteExtension($extraGlobals));
        App::twig($twigEnvironment);
    }

    /**
     * Configure Twig.
     * @param bool $cache Should templates be cached?
     * @param string $cachePath The path to the directory where cached templates will be stored.
     * @param string $baseDir The base path to where templates are located.
     * @param array $extraGlobals Some extra globals from App::init
     * @return TwigConfig
     */
    public static function init($cache, $cachePath, $baseDir, $extraGlobals) {
        return new TwigConfig($cache, $cachePath, $baseDir, $extraGlobals);
    }
}


class SimpleSiteExtension extends Twig_Extension {
    private $extraGlobals = array();

    public function SimpleSiteExtension($extraGlobals) {
        $this->extraGlobals = $extraGlobals;
    }

    public function getName() {
        return 'simplesite';
    }

    public function getGlobals() {
        return ($this->extraGlobals + array(
            'sitemap' => App::sitemap()
        ));
    }

    public function getFunctions() {
        return array(
            '*_snippet' => new Twig_Function_Method($this, 'snippet'),
            'get_location' => new Twig_Function_Method($this, 'get_location'),
            'href' => new Twig_Function_Method($this, 'href'),
            '*_data' => new Twig_Function_Method($this, 'get_data')
        );
    }

    /**
     * This is a wildcard function where the first part of the function name determines which Snippet we retrieve.
     * @param string $name The wildcard portion of the function name (should be the Snippet name).
     * @return null|Snippet
     */
    public function snippet($name) {
        return App::getSnippetInstance($name);
    }

    /**
     * Retrieve a Location instance by name.
     * @param string $name The name of the Location to be retrieved.
     * @return Location|null
     */
    public function get_location($name) {
        return Sitemap::location($name);
    }

    /**
     * Retrieve a URL for a Location instance by Location's name.
     * @param string $name The name of the Location.
     * @param bool $absolute Should the URL be made absolute?
     * @return string
     */
    public function href($name, $absolute = false) {
        return Sitemap::location($name)->getUrl($absolute);
    }

    /**
     * Get data by key.
     * @param string $key1 The data's key.
     * @param string|null $key2 The key to retrieve from within the data.
     * @return Data|mixed
     */
    public function get_data($key1, $key2 = null) {
        if (is_null($key2)) return App::data($key1);
        else return App::data($key1)->get($key2);
    }
}