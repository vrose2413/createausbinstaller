<?php

class SitemapsHtml
{

    public function main()
    {
        $fn = new Functions();
        $files = $fn->get_all_kw(DIRPATH . 'keywords');
        $content = '';
        for ($i = 0; $i < count($files); $i++) {
            $content .= '<a href="' . web['url'] . '/sitemaps/' . str_replace('.txt', '', $files[$i]) . '/"><button class="text-white ' . $fn->random_bg() . '-500 hover:' . $fn->random_bg() . '-600 px-4 py-1 text-xs rounded-lg leading-loose mx-1 mb-1 shadow">Sitemaps ' . str_replace('.txt', '', $files[$i]) . '</button></a>';
        }
        return $content;
    }

    public function single($file)
    {
        $fn = new Functions();
        $file = @file(DIRPATH . 'keywords/' . $file . '.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $content = '';
        for ($i = 0; $i < count($file); $i++) {
            $content .= '<a href="' . web['url'] . '/' . $fn->slugify($file[$i]) . '/">
            <button class="-ml-1 text-left text-white ' . $fn->random_bg() . '-500 hover:' . $fn->random_bg() . '-600 px-4 py-1 text-xs rounded-r-full leading-loose mb-1 shadow-inner">' . ucwords($file[$i]) . '</button></a>';
        }
        return $content;
    }
}
