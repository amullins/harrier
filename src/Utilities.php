<?php

/**
 * Class Utilities
 */
final class Utilities {

    private function Utilities() {}

    /**
     * @param string $haystack String to search.
     * @param string $needle String to find.
     * @return bool
     */
    public static function starts_with($haystack, $needle) {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    /**
     * @param string $haystack String to search.
     * @param string $needle String to find.
     * @return bool
     */
    public static function ends_with($haystack, $needle) {
        $length = strlen($needle);

        if ($length == 0) return true;

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * @param string $url A relative URL likely from $_SERVER['REQUEST_URI']
     * @return string An absolute URL
     */
    public static function makeAbsoluteUrl($url) {
        $scheme = isset($_SERVER['HTTPS']) ? 'https' : 'http';
        return $scheme . '://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $url;
    }
}