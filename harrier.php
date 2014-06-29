<?php

include 'src/App.php';


App::init(array(
    'mail-username' => '',
    'mail-password' => ''
));

LocationParam::defaults(
    Meta::define(array(
        'description' => "Harrier is a small PHP framework for building simple websites.",
        'keywords' => 'php framework'
    ), true, true)
);

App::snippets(array(
    'sample' => 'SampleSnippet'
));

App::sitemap(
    Location::create('home', 'Harrier',
        Route::define(array(''), array('index.twig'))
    )
);

App::sitemap(
    new ErrorLocation(404, "Whoops. Couldn't find that page.", array('404.twig'))
);

App::processRequest();