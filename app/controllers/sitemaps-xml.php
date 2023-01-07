<?php

class SitemapXML
{
    public $i = 1;
    public $interval = -1;

    public function index()
    {
        $fn = new Functions();
        $files = $fn->get_all_kw(DIRPATH . 'keywords');
        echo "<?xml version='1.0' encoding='UTF-8'?>\n";
        echo '<?xml-stylesheet type="text/xsl" href="/public/assets/sitemap.xsl"?>' . "\n";
        echo "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        echo "\t<sitemap>\n";
        echo "\t\t<loc><![CDATA[" . web['url'] . "/sitemap/pages.xml" . "]]></loc>\n";
        echo "\t\t<lastmod><![CDATA[" . date('Y-m-d\TH:m:s+07:00') . "]]></lastmod>\n";
        echo "\t</sitemap>\n";
        echo "\t<sitemap>\n";
        echo "\t\t<loc><![CDATA[" . web['url'] . "/rss.xml" . "]]></loc>\n";
        echo "\t\t<lastmod><![CDATA[" . date('Y-m-d\TH:m:s+07:00') . "]]></lastmod>\n";
        echo "\t</sitemap>\n";
        echo "\t<sitemap>\n";
        echo "\t\t<loc><![CDATA[" . web['url'] . "/feed" . "]]></loc>\n";
        echo "\t\t<lastmod><![CDATA[" . date('Y-m-d\TH:m:s+07:00') . "]]></lastmod>\n";
        echo "\t</sitemap>\n";
        foreach ($files as $page) {
            echo "\t<sitemap>\n";
            echo "\t\t<loc><![CDATA[" . web['url'] . "/sitemap/" . str_replace(".txt", "", $page) . ".xml]]></loc>\n";
            echo "\t\t<lastmod><![CDATA[" . date('Y-m-d\TH:m:s+07:00') . "]]></lastmod>\n";
            echo "\t</sitemap>\n";
        }
        echo "</sitemapindex>";
    }

    public function pages()
    {
        echo "<?xml version='1.0' encoding='UTF-8'?>\n";
        echo '<?xml-stylesheet type="text/xsl" href="/public/assets/sitemap.xsl"?>' . "\n";
        echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        echo "\t<url>\n";
        echo "\t\t<loc><![CDATA[" . web['url'] . "/" . "]]></loc>\n";
        echo "\t\t<lastmod><![CDATA[" . date('Y-m-d\TH:m:s+07:00') . "]]></lastmod>\n";
        echo "\t\t<changefreq><![CDATA[daily]]></changefreq>\n";
        echo "\t\t<priority><![CDATA[1.0]]></priority>\n";
        echo "\t</url>\n";
        echo "\t<url>\n";
        echo "\t\t<loc><![CDATA[" . web['url'] . "/p/contact/]]></loc>\n";
        echo "\t\t<lastmod><![CDATA[" . date('Y-m-d\TH:m:s+07:00') . "]]></lastmod>\n";
        echo "\t\t<changefreq><![CDATA[never]]></changefreq>\n";
        echo "\t\t<priority><![CDATA[0.1]]></priority>\n";
        echo "\t</url>\n";
        echo "\t<url>\n";
        echo "\t\t<loc><![CDATA[" . web['url'] . "/p/copyright/]]></loc>\n";
        echo "\t\t<lastmod><![CDATA[" . date('Y-m-d\TH:m:s+07:00') . "]]></lastmod>\n";
        echo "\t\t<changefreq><![CDATA[never]]></changefreq>\n";
        echo "\t\t<priority><![CDATA[0.1]]></priority>\n";
        echo "\t</url>\n";
        echo "\t<url>\n";
        echo "\t\t<loc><![CDATA[" . web['url'] . "/p/dmca/]]></loc>\n";
        echo "\t\t<lastmod><![CDATA[" . date('Y-m-d\TH:m:s+07:00') . "]]></lastmod>\n";
        echo "\t\t<changefreq><![CDATA[never]]></changefreq>\n";
        echo "\t\t<priority><![CDATA[0.1]]></priority>\n";
        echo "\t</url>\n";
        echo "\t<url>\n";
        echo "\t\t<loc><![CDATA[" . web['url'] . "/p/privacy-policy/]]></loc>\n";
        echo "\t\t<lastmod><![CDATA[" . date('Y-m-d\TH:m:s+07:00') . "]]></lastmod>\n";
        echo "\t\t<changefreq><![CDATA[never]]></changefreq>\n";
        echo "\t\t<priority><![CDATA[0.1]]></priority>\n";
        echo "\t</url>\n";
        echo "\t<url>\n";
        echo "\t\t<loc><![CDATA[" . web['url'] . "/sitemaps/main/]]></loc>\n";
        echo "\t\t<lastmod><![CDATA[" . date('Y-m-d\TH:m:s+07:00') . "]]></lastmod>\n";
        echo "\t\t<changefreq><![CDATA[weekly]]></changefreq>\n";
        echo "\t\t<priority><![CDATA[0.1]]></priority>\n";
        echo "\t</url>\n";
        echo '</urlset>';
    }

    public function posts($file)
    {
        $fn = new Functions();
        $file = @file(DIRPATH . 'keywords/' . $file . '.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        echo "<?xml version='1.0' encoding='UTF-8'?>\n";
        echo '<?xml-stylesheet type="text/xsl" href="/public/assets/sitemap.xsl"?>' . "\n";
        echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($file as $link) {
            echo "\t<url>\n";
            echo "\t\t<loc><![CDATA[" . web['url'] . "/" . $fn->slugify($link) . "/]]></loc>\n";
            echo "\t\t<lastmod><![CDATA[" . date('Y-m-d\TH:m:s+07:00') . "]]></lastmod>\n";
            echo "\t\t<changefreq><![CDATA[never]]></changefreq>\n";
            echo "\t\t<priority><![CDATA[0.6]]></priority>\n";
            echo "\t</url>\n";
        }
        echo '</urlset>';
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
