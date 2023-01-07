<?php

class Rss
{
    public $i = 1;
    public $interval = -1;

    public function index()
    {
        $fn = new Functions();
        $files = $fn->get_all_kw(DIRPATH . 'keywords');

        header("Content-Type: application/xml; charset=ISO-8859-1");
        echo '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">' . "\n";
        echo '<channel>' . "\n";
        echo '' . "\t" . '<title>' . web['title'] . '</title>' . "\n";
        echo '' . "\t" . '<atom:link href="' . web['url'] . '/feed" rel="self" type="application/rss+xml" />' . "\n";
        echo '' . "\t" . '<link>' . web['url'] . '/</link>' . "\n";
        echo '' . "\t" . '<description>' . web['description'] . '</description>' . "\n";
        echo '' . "\t" . '<lastBuildDate>' . date("l, d M Y H:m:s +07:00") . '</lastBuildDate>' . "\n";
        echo '' . "\t" . '<language>en-US</language>' . "\n";
        echo '' . "\t" . '<sy:updatePeriod>hourly</sy:updatePeriod>' . "\n";
        echo '' . "\t" . '<sy:updateFrequency>1</sy:updateFrequency>' . "\n";
        echo '' . "\t" . '<generator>' . strtoupper(web['domain']) . '</generator>' . "\n";
        foreach ($files as $r) {
            echo "\t\t<item>\n";
            echo "\t\t\t<title>" . web['title'] . ' RSS ' . str_replace(".txt", "", $r) . "</title>\n";
            echo "\t\t\t<description>This is RSS " . str_replace(".txt", "", $r) . " of " . strtoupper(web['domain']) . "</description>\n";
            echo "\t\t\t<link>" . web['url'] . "/rss/" . str_replace(".txt", "", $r) . "/</link>\n";
            echo "\t\t\t<pubDate>" . date("Y-m-d\TH:m:s+07:00") . "</pubDate>\n";
            echo "\t\t</item>\n";
        }
        echo "</channel>\n";
        echo "</rss>";
    }

    public function posts($file)
    {
        $fn = new Functions();
        $file = @file(DIRPATH . 'keywords/' . $file . '.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        header("Content-Type: application/xml; charset=ISO-8859-1");
        echo '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">' . "\n";
        echo '<channel>' . "\n";
        echo '' . "\t" . '<title>' . web['title'] . '</title>' . "\n";
        echo '' . "\t" . '<atom:link href="' . web['url'] . '/feed" rel="self" type="application/rss+xml" />' . "\n";
        echo '' . "\t" . '<link>' . web['url'] . '/</link>' . "\n";
        echo '' . "\t" . '<description>' . web['description'] . '</description>' . "\n";
        echo '' . "\t" . '<lastBuildDate>' . date("l, d M Y H:m:s +07:00") . '</lastBuildDate>' . "\n";
        echo '' . "\t" . '<language>en-US</language>' . "\n";
        echo '' . "\t" . '<sy:updatePeriod>hourly</sy:updatePeriod>' . "\n";
        echo '' . "\t" . '<sy:updateFrequency>1</sy:updateFrequency>' . "\n";
        echo '' . "\t" . '<generator>' . strtoupper(web['domain']) . '</generator>' . "\n";
        foreach ($file as $k) {
            $k = preg_replace("/[^a-zA-Z0-9\s]+/", "", $k);
            echo "\t\t<item>\n";
            echo "\t\t\t<title>" . ucwords($k) . "</title>\n";
            echo "\t\t\t<description>Read Or Download " . ucwords($k) . " at " . strtoupper(web['domain']) . "</description>\n";
            echo "\t\t\t<link>" . web['url'] . "/" . $fn->slugify($k) . "/</link>\n";
            echo "\t\t\t<pubDate>" . date("Y-m-d\TH:m:s+07:00") . "</pubDate>\n";
            echo "\t\t</item>\n";
        }
        echo "</channel>\n";
        echo "</rss>";
    }

    public function feed()
    {
        $fn = new Functions();
        $ran_files = $fn->random_files(DIRPATH . 'keywords', '.txt');
        $file = @file($ran_files, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        header("Content-Type: application/xml; charset=ISO-8859-1");
        echo '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">' . "\n";
        echo '<channel>' . "\n";
        echo '' . "\t" . '<title>' . web['title'] . '</title>' . "\n";
        echo '' . "\t" . '<atom:link href="' . web['url'] . '/feed" rel="self" type="application/rss+xml" />' . "\n";
        echo '' . "\t" . '<link>' . web['url'] . '/</link>' . "\n";
        echo '' . "\t" . '<description>' . web['description'] . '</description>' . "\n";
        echo '' . "\t" . '<lastBuildDate>' . date("l, d M Y H:m:s +07:00") . '</lastBuildDate>' . "\n";
        echo '' . "\t" . '<language>en-US</language>' . "\n";
        echo '' . "\t" . '<sy:updatePeriod>hourly</sy:updatePeriod>' . "\n";
        echo '' . "\t" . '<sy:updateFrequency>1</sy:updateFrequency>' . "\n";
        echo '' . "\t" . '<generator>' . strtoupper(web['domain']) . '</generator>' . "\n";
        foreach ($file as $k) {
            $k = preg_replace("/[^a-zA-Z0-9\s]+/", "", $k);
            echo "\t\t<item>\n";
            echo "\t\t\t<title>" . ucwords($k) . "</title>\n";
            echo "\t\t\t<description>Read Or Download " . ucwords($k) . " at " . strtoupper(web['domain']) . "</description>\n";
            echo "\t\t\t<link>" . web['url'] . "/" . $fn->slugify($k) . "/</link>\n";
            echo "\t\t\t<pubDate>" . date("Y-m-d\TH:m:s+07:00") . "</pubDate>\n";
            echo "\t\t</item>\n";
        }
        echo "</channel>\n";
        echo "</rss>";
    }

    public function getTanggal($interval)
    {
        $h = rand(0, 23);
        $m = rand(10, 59);
        $s = rand(10, 59);

        $tanggal = date("d");
        $bulan = date("m");
        $tahun = date("Y");
        return mktime($interval, 0, $s, $bulan, $tanggal, $tahun);
    }
}
