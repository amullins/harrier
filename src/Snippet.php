<?php

class Snippet {
    public $name = '';

    public function Snippet($name) {
        $this->name = $name;
    }

    /**
     * This function is executed for ALL requests.
     */
    public function init() {
        (App::sitemap()->request->method == 'post') ? $this->post_page_init() : $this->get_page_init();
    }

    /**
     * This function is executed for POST requests.
     * Use it for things like form processing.
     */
    public function post_page_init() {}

    /**
     * This function is executed for GET requests.
     */
    public function get_page_init() {}
}