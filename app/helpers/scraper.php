<?php

require_once 'dom.php';

function got($url, $referer = '', $ua = '', $method = '')
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => array(
            "referer: $referer",
            "user-agent: $ua",
        ),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => -1,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method
    ));
    return curl_exec($curl);
    curl_close($curl);
}

function scrape($query)
{
    $referer = 'https://html.duckduckgo.com/';
    // $ua = 'Mozilla/5.0 (compatible; DuckDuckGo-Favicons-Bot/1.0; +http://duckduckgo.com)';
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $method = 'POST';
    $url = 'https://html.duckduckgo.com/html/?q=' . urlencode($query);
    $html = got($url, $referer, $ua, $method);
    return $html;
    $html->clear();
    unset($html);
}

function scrapeText($query)
{
    $engines = ['https://www.bing.com', 'https://html.duckduckgo.com/html/'];
    $engine = $engines[array_rand($engines)];
    $results = [];

    if (strpos($engine, 'duckduckgo.com') !== false) {
        $html = scrape($query);
        $html = str_get_html($html);

        foreach ($html->find('a[class="result__snippet"]') as $e) {
            if ($e->innertext != '') {
                array_push($results, str_replace(array(' ...', '...'), '.', $e->innertext));
            }
        }
    } elseif (strpos($engine, 'bing.com') !== false) {
        $first = ['5', '16'];
        foreach ($first as $i) {
            $referer = 'https://www.google.com/';
            $method = 'GET';
            $ua = 'Mozilla/5.0 (Linux; U; Android 8.1.0; en-US; Nexus 6P Build/OPM7.181205.001) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.11.1.1197 Mobile Safari/537.36';
            $url = 'https://www.bing.com/search?q=' . urlencode($query) . '&first=' . $i . '&FORM=PQRE';
            $html = got($url, $referer, $ua, $method);
            $html = str_get_html($html);

            foreach ($html->find('div.b_caption p') as $e) {
                array_push($results, $e->innertext);
            }

            $html->clear();
            unset($html);
        }

        for ($i = 0; $i < count($results); $i++) {
            $res = preg_replace("/<span(.*)span>/", "", $results[$i]);
            $results[$i] = str_replace(array('&nbsp;&#0183;&#32;', ' …', '…'), '', $res);
        }
    }

    return $results;
}

function getToken($query)
{
    $referer = 'https://duckduckgo.com/';
	$method = 'GET';
	$ua = $_SERVER['HTTP_USER_AGENT'];
    $url = "http://duckduckgo.com?" .
        http_build_query([
            "q" => $query,
            "t" => "h_",
            "iax" => "images",
            "ia" => "images",
        ]);
    $html = got($url, $referer, $ua, $method);

    $vqd_token = "";
    if (
        !preg_match("/vqd\s*\=\s*\'(?<vqd_token>[^\']*)/", $html, $matches)
    ) {
        throw new \Exception("Error: Banned IP. We will rest for a bit");
    }

    $vqd_token = $matches["vqd_token"];

    return $vqd_token;
}


function scrapeImage($query)
{
    $images = [];
    $engines = ['https://www.bing.com', 'https://duckduckgo.com'];
    $engine = $engines[array_rand($engines)];


    if (strpos($engine, 'duckduckgo.com') !== false) {
        $token = getToken($query);
        $referer = 'https://www.google.com/';
        // $ua = 'Mozilla/5.0 (compatible; DuckDuckGo-Favicons-Bot/1.0; +http://duckduckgo.com)';
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $method = 'GET';
        $url = $engine . "/i.js?l=wt-wt&o=json&q=" . urlencode($query) . "&vqd=" . $token . "&f=,,,,,&p=1&v7exp=a&sltexp=b&s=";
        $json = got($url, $referer, $ua, $method);

        if (
            !preg_match('/"results":(?<images_json>.+?\}\])/m', $json, $matches)
        ) {
            throw new \Exception("Error: unable to extract images json...");
        }

        $images_json = $matches["images_json"];
        $images = json_decode($images_json, true);

        foreach ($images as $key => $image) {
            $images[$key]["title"] = preg_replace("/[^a-zA-Z0-9\s]/", ' ', $image["title"]);
            $images[$key]["title"] = str_replace(array('  ', '   '), ' ', $images[$key]["title"]);
        }
    } elseif (strpos($engine, 'bing.com') !== false) {
        $first = ['1', '29', '57'];
        foreach ($first as $i) {
            $ua = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)';
            $method = 'GET';
            $url = $engine . '/images/search?q=' . urlencode($query) . '&first=' . $i . '&count=28&FORM=IBASEP';
            $referer = 'https://www.bing.com/';
            $html = got($url, $referer, $ua, $method);
            $html = str_get_html($html);
            foreach ($html->find('div.item') as $e) {
                $arr_images = [
                    'image' => $e->find('a.thumb', 0)->href,
                    'thumbnail' => $e->find('a.thumb div.cico img', 0)->src,
                    'source' => $e->find('div.meta a.tit', 0)->plaintext,
                    'title' => $e->find('div.meta div.des', 0)->plaintext
                ];
                array_push($images, $arr_images);
            }

            foreach ($images as $key => $image) {
                $images[$key]["title"] = preg_replace("/[^a-zA-Z0-9\s]/", ' ', $image["title"]);
                $images[$key]["title"] = str_replace(array('  ', '   '), ' ', $images[$key]["title"]);
            }
            $html->clear();
            unset($html);
        }
    }

    return $images;
}
