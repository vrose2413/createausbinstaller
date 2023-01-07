<?php

class Functions
{
    public function slugify($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '_');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return $text;
    }

    public function limit_words($string, $word_limit)
    {
        $words = explode(" ", $string);
        return implode(" ", array_splice($words, 0, $word_limit));
    }

    public function get_all_kw($dir)
    {
        $data = '';
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $data .= $entry . "\n";
            }
        }
        closedir($handle);
        $data = trim($data);
        if ($data != "") {
            $data = explode("\n", $data);
            sort($data, SORT_NATURAL | SORT_FLAG_CASE);
        }
        return $data;
    }

    public function random_files($dir, $slug)
    {
        $files = glob($dir . '/*' . $slug);
        $file = array_rand($files);
        return $files[$file];
    }

    public function random_keyword()
    {
        $dir = DIRPATH . 'keywords';
        $files = $this->random_files($dir, '.txt');
        $files = file($files, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        shuffle($files);
        return $files;
    }

    public function random_bg()
    {
        $lbg = array('bg-green', 'bg-blue', 'bg-indigo', 'bg-red', 'bg-yellow', 'bg-orange', 'bg-teal');
        $bg = array_rand($lbg);
        return $lbg[$bg];
    }
}
