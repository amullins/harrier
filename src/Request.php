<?php


/**
 * Class Request
 */
final class Request {

    /** @var string */
    public $scheme = 'http';

    /** @var string */
    public $host = null;

    /** @var int */
    public $port = null;

    /** @var string */
    public $user = null;

    /** @var string */
    public $pass = null;

    /** @var array */
    public $path = null;

    /** @var string */
    public $params = null;

    /** @var string */
    public $fragment = null;

    /** @var string */
    public $method = null;

    public function Request($url) {
        $urlParts = parse_url(Utilities::makeAbsoluteUrl($url));
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);

        foreach($urlParts as $key => $value) {
            switch ($key) {
                case 'path' :
                    $this->path = explode('/', substr($value, 1));
                    break;
                case 'query' :
                    $this->params = $value;
                    break;
                default :
                    $this->$key = $value;
            }
        }
    }
}