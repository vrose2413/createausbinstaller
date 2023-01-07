<?php

require_once APPPATH . 'helpers/scraper.php';
require_once APPPATH . 'helpers/functions.php';

class Posts
{
    public function images($query)
    {
        try {
            $images = scrapeImage($query);
            return $images;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function png($query)
    {
        $fn = new Functions();
        try {
            $images = scrapeImage($fn->limit_words($query, 7));
            $png = file_get_contents($images[rand(0, count($images))]['thumbnail']);
            header('Content-Type: image/png');
            header("accept-encoding: gzip, deflate, br");
            header("vary: Accept-Encoding");
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            print_r($png);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
