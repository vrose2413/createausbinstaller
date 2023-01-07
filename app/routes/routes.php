<?php

$router = new \Bramus\Router\Router();
require_once APPPATH . 'controllers/posts.php';

$router->get('/', function () {
    include_once APPPATH . 'cache/load.php';
    $title_post = 'Home';
    $fn = new Functions();
    $keywords_lists = $fn->random_keyword(DIRPATH . 'keywords', '.txt');

    if ($fn->random_keyword()[0] != '') {
        $gen = new Posts();
        $images = $gen->images($fn->limit_words($fn->random_keyword()[0], 7));
    } else {
        die("Keywords not found.<br><br><strong>NOTE:</strong> Open folder /public/keywords/ and create new file with filetype .TXT");
    }

    // ADS
    $top_out = file_get_contents(DIRPATH . 'ads/top_out_article.txt');
    $top_side = file_get_contents(DIRPATH . 'ads/sidebar.txt');
    $top_in = file_get_contents(DIRPATH . 'ads/top_in_article.txt');
    $mid_in = file_get_contents(DIRPATH . 'ads/mid_in_article.txt');

    include_once VIEWS . 'home.php';
    include_once APPPATH . 'cache/save.php';
});

$router->get('/ping',  function () {
    $fn = new Functions();
    $data = $fn->get_all_kw(DIRPATH . 'keywords');
    include_once VIEWS . 'ping.php';
});

$router->get('/robots.txt',  function () {
    include_once VIEWS . 'robots.txt.php';
});

$router->get('/sitemap.xml',  function () {
    header('Content-Type: application/xml; charset=UTF-8');
    require_once APPPATH . 'controllers/sitemaps-xml.php';
    $sitemap = new SitemapXML();
    $sitemap->index();
});

$router->get('/sitemap/{query}.xml', function ($query) {
    header('Content-Type: application/xml; charset=UTF-8');
    require_once APPPATH . 'controllers/sitemaps-xml.php';
    $query = htmlentities($query);
    $sitemap = new SitemapXML();
    $fn = new Functions();
    $files = $fn->get_all_kw(DIRPATH . 'keywords');
    if ($query == 'pages') {
        $sitemap->pages();
    } else {
        if (in_array($query . '.txt', $files)) {
            $sitemap->posts($query);
        } else {
            include_once DIRPATH . '404.php';
        }
    }
});

$router->get('/rss.xml',  function () {
    require_once APPPATH . 'controllers/rss.php';
    $rss = new Rss();
    $rss->index();
});

$router->get('/rss/{query}',  function ($query) {
    require_once APPPATH . 'controllers/rss.php';
    $rss = new Rss();
    $fn = new Functions();
    $files = $fn->get_all_kw(DIRPATH . 'keywords');

    if (in_array($query . '.txt', $files)) {
        $rss->posts($query);
    } else {
        include_once DIRPATH . '404.php';
    }
});

$router->get('/feed',  function () {
    require_once APPPATH . 'controllers/rss.php';
    $rss = new Rss();
    $rss->feed();
});

$router->get('/sitemaps', function () {
    header('Location: /sitemaps/main/');
});

$router->get('/sitemaps/{query}', function ($query) {
    require_once APPPATH . 'controllers/sitemaps-html.php';
    $query = htmlentities($query);
    $fn = new Functions();
    $files = $fn->get_all_kw(DIRPATH . 'keywords');
    $sitemaps = new SitemapsHtml();
    if ($query == 'main') {
        $title_post = "Sitemaps: $query";
        $contents = $sitemaps->main();
        include_once VIEWS . 'sitemaps-html/main.php';
    } else {
        if (in_array($query . '.txt', $files)) {
            $title_post = "Sitemaps: $query";
            $contents = $sitemaps->single($query);
            include_once VIEWS . 'sitemaps-html/main.php';
        } else {
            include_once DIRPATH . '404.php';
        }
    }
});

$router->get('/p/{query}',  function ($query) {
    $query = htmlentities($query);
    if (file_exists(VIEWS . 'pages/' . $query . '.php')) {
        $title_post = ucwords(str_replace('-', ' ', $query));
        $fn = new Functions();
        $keywords_lists = $fn->random_keyword(DIRPATH . 'keywords', '.txt');
        header("Content-Type: text/html; charset=UTF-8");
        header("accept-encoding: gzip, deflate, br");

        // ADS
        $top_out = file_get_contents(DIRPATH . 'ads/top_out_article.txt');
        $top_side = file_get_contents(DIRPATH . 'ads/sidebar.txt');
        $top_in = file_get_contents(DIRPATH . 'ads/top_in_article.txt');
        $mid_in = file_get_contents(DIRPATH . 'ads/mid_in_article.txt');

        include_once VIEWS . 'pages/' . $query . '.php';
    } else {
        include_once DIRPATH . '404.php';
    }
});

$router->get('/{query}.png',  function ($query) {
    $query = htmlentities($query);
    $query = str_replace('-', ' ', $query);
    $gen = new Posts();
    $gen->png($query);
});

$router->get('/{query}',  function ($query) {
    $fn = new Functions();
    $gen = new Posts();
    $query = htmlentities($query);
    $query = str_replace('-', ' ', $query);
    $title_post = ucwords(urldecode(strtolower($query)));
    $images = $gen->images($fn->limit_words($query, 7));
    if (count($images) < 1) {
        $images = $gen->images($fn->limit_words($query, 6));
    }
    $keywords_lists = $fn->random_keyword();
    $related = $fn->random_keyword();

    // ADS
    $top_out = file_get_contents(DIRPATH . 'ads/top_out_article.txt');
    $top_side = file_get_contents(DIRPATH . 'ads/sidebar.txt');
    $top_in = file_get_contents(DIRPATH . 'ads/top_in_article.txt');
    $mid_in = file_get_contents(DIRPATH . 'ads/mid_in_article.txt');

    include_once VIEWS . 'single.php';
});

$router->run();
