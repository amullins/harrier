<?php


/**
 * Class ErrorLocation
 */
class ErrorLocation extends Location {
    /** @var int The HTTP error code for which this Location is defined. */
    public $code = null;

    /** @var array The array representation of the template location. */
    private $template = null;

    /**
     * @param int $code @see ErrorLocation::$code
     * @param mixed $title @see Location::$title
     * @param array $template @see ErrorLocation::$template
     */
    public function ErrorLocation($code, $title, $template) {
        $this->code = $code;
        $this->title = $title;
        $this->template = $template;
    }

    /** @see Location::process() */
    public function process() {
        http_response_code($this->code);

        echo App::twig()->render(
            implode('/', $this->template),
            array(
                'location' => App::location()
            )
        );
    }
}