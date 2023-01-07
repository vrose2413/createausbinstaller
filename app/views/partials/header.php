<?php
$uri = $_SERVER['REQUEST_URI'];
if ($uri == '/' || strpos($uri, 'p/contact') || strpos($uri, 'p/copyright') || strpos($uri, 'p/dmca') || strpos($uri, 'p/privacy-policy') || strpos($uri, 'sitemaps/')) {
} else {
    $kw = strtolower($title_post);
    $res = scrapeText($fn->limit_words($kw, 7));
    if (count($res) < 1) {
        $res = scrapeText($fn->limit_words($kw, 6));
    }
    $desc = str_replace(array('<b>', '</b>', '<strong>', '</strong>'), '', $res[array_rand($res)]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1, shrink-to-fit=no" />
    <title><?= $title_post ?> - <?= web['title'] ?></title>
    <meta name="description" content="<?php if ($_SERVER['REQUEST_URI'] == '/') {
                                            echo web['description'];
                                        } elseif (count($res) > 3) {
                                            echo $desc;
                                        } else {
                                            echo "In this particular article we will give you some of the highlights of $title_post. We all hope that you can actually search about $title_post here ...";
                                        }
                                        ?>" />
    <meta name="keywords" content="<?php if ($_SERVER['REQUEST_URI'] == '/') {
                                        echo web['keyword'];
                                    } else {
                                        echo strtolower($title_post) . ', ' . strtolower(web['title']) . ', ' . str_replace(' ', ', ', strtolower($title_post));
                                    }
                                    ?>" />
    <meta name="robots" content="index, follow" />
    <meta name="googlebot" content="index, follow" />
    <link rel="canonical" href="<?= web['full_url'] ?>" />
    <meta content='<?php if ($_SERVER['REQUEST_URI'] == '/') {
                        echo web['title'];
                    } else {
                        echo $title_post;
                    } ?>' property='og:title' />
    <meta content='<?php if ($_SERVER['REQUEST_URI'] == '/') {
                        echo 'website';
                    } else {
                        echo 'article';
                    } ?>' property='og:type' />
    <meta content='<?= web['full_url'] ?>' property='og:url' />
    <meta content='<?php if ($images[0]['image'] == '') {
                        echo web['url'] . '/public/assets/img/image.jpg';
                    } else {
                        echo $images[0]['image'];
                    } ?>' property='og:image' />
    <meta content='<?php if ($_SERVER['REQUEST_URI'] == '/') {
                        echo web['description'];
                    } elseif (count($res) > 3) {
                        echo $desc;
                    } else {
                        echo "In this particular article we will give you some of the highlights of $title_post. We all hope that you can actually search about $title_post here ...";
                    }
                    ?>' property='og:description' />
    <meta content='<?= web['title'] ?>' property='og:site_name' />
    <meta content="<?php if ($_SERVER['REQUEST_URI'] == '/') {
                        echo web['title'];
                    } else {
                        echo $title_post;
                    } ?>" property="og:image:alt" />
    <meta content="<?= web['title'] ?>" name="twitter:site" />
    <meta content="summary_large_image" name="twitter:card" />
    <meta content='<?= web['author'] ?>' name='Author' />
    <link href="<?= web['url'] ?>/rss.xml" rel="alternate" title="<?= web['title'] ?> - RSS" type="application/rss+xml" />
    <link href="<?= web['url'] ?>/feed" rel="alternate" title="<?= web['title'] ?> - Feed" type="application/rss+xml" />
    <link rel="shortcut icon" href="<?= web['icon'] ?>" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/css/lightbox.min.css" integrity="sha256-tBxlolRHP9uMsEFKVk+hk//ekOlXOixLKvye5W2WR5c=" crossorigin="anonymous" />
    <link rel="stylesheet" href="/public/assets/css/style.css" media="all" />
    <style>
        #menu-toggle:checked+#menu {
            display: block;
        }
    </style>
    <?php if ($uri == '/' || strpos($uri, 'p/contact') || strpos($uri, 'p/copyright') || strpos($uri, 'p/dmca') || strpos($uri, 'p/privacy-policy') || strpos($uri, 'sitemaps/')) : ?>
        <script type="application/ld+json">
            {
                "@context": "http:\/\/schema.org",
                "@type": "WebPage",
                "publisher": {
                    "@type": "Organization",
                    "name": <?= json_encode(web['title']) ?>,
                    "logo": {
                        "@type": "ImageObject",
                        "url": <?= json_encode(web['url'] . web['icon']) ?>,
                        "width": 512,
                        "height": 512
                    }
                },
                "headline": <?= json_encode($title_post) ?>,
                "url": <?= json_encode(web['full_url']) ?>

            }
        </script>
    <?php else : ?>
        <script type="application/ld+json">
            {
                "@context": "http:\/\/schema.org",
                "@type": "Article",
                "publisher": {
                    "@type": "Organization",
                    "name": <?= json_encode(web['title']) ?>,
                    "logo": {
                        "@type": "ImageObject",
                        "url": <?= json_encode(web['url'] . web['icon']) ?>,
                        "width": 512,
                        "height": 512
                    }
                },
                "headline": <?= json_encode($title_post) ?>,
                "url": <?= json_encode(web['full_url']) ?>,
                "thumbnailUrl": <?php if (count($images) > 3) {
                                    echo json_encode($images[array_rand($images)]['thumbnail']);
                                } else {
                                    echo json_encode(web['url'] . '/public/assets/img/image.jpg');
                                } ?>,
                "dateCreated": "<?= date("Y-m-d\TH:m:s+07:00", strtotime("-4 hour")) ?>",
                "datePublished": "<?= date("Y-m-d\TH:m:s+07:00", strtotime("-2 hour")) ?>",
                "dateModified": "<?= date("Y-m-d\TH:m:s+07:00") ?>",
                "description": <?php if (count($res) > 3) {
                                    echo json_encode($desc);
                                } else {
                                    echo json_encode("In this particular article we will give you some of the highlights of $title_post. We all hope that you can actually search about $title_post here ...");
                                } ?>,
                "creator": [
                    <?= json_encode(web['author']) ?>

                ],
                "author": [{
                    "@type": "Person",
                    "name": <?= json_encode(web['author']) ?>

                }],
                "image": <?= json_encode($images[array_rand($images)]['image']) ?>,
                "keywords": [
                    <?= json_encode(strtolower($title_post)) ?>

                ]
            }
        </script>
    <?php endif ?>
    <?php
    $inject_head = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/inject-head.txt');
    if ($inject_head != '') {
        print_r($inject_head);
    }
    ?>

</head>

<body class="antialiased text-gray-900 bg-gray-200 font-display">
    <header class="lg:px-16 px-6 bg-gray-800 flex flex-wrap items-center lg:py-0 py-2 shadow">
        <div class="flex items-center">
            <svg class="w-6 h-6" viewBox="0 0 512 512" style="enable-background: new 0 0 512 512;" xml:space="preserve">
                <path style="fill: #ff6536;" d="M54.211,249.7c0,0,20.228,29.717,62.624,54.871c0,0-30.705-259.502,169.358-304.571 c-51.257,188.121,65.2,241.174,107.651,141.786c70.893,94.651,17.066,177.229,17.066,177.229 c29.069,4.188,53.487-27.57,53.487-27.57c0.218,3.912,0.34,7.851,0.34,11.818C464.738,418.545,371.283,512,256,512 S47.262,418.545,47.262,303.262C47.262,284.744,49.686,266.794,54.211,249.7z">
                    <path style="fill: #ff421d;" d="M464.398,291.445c0,0-24.418,31.758-53.487,27.57c0,0,53.827-82.578-17.066-177.229 C351.394,241.174,234.937,188.121,286.194,0C275.479,2.414,265.431,5.447,256,9.018V512c115.283,0,208.738-93.455,208.738-208.738 C464.738,299.295,464.616,295.357,464.398,291.445z">
                        <path style="fill: #fbbf00;" d="M164.456,420.456C164.456,471.014,205.442,512,256,512s91.544-40.986,91.544-91.544 c0-27.061-11.741-51.379-30.408-68.138c-35.394,48.085-85.832-24.856-46.524-78.122 C270.612,274.196,164.456,287.499,164.456,420.456z">
                            <path style="fill: #ffa900;" d="M347.544,420.456c0-27.061-11.741-51.379-30.408-68.138c-35.394,48.085-85.832-24.856-46.524-78.122 c0,0-5.768,0.725-14.612,3.516V512C306.558,512,347.544,471.014,347.544,420.456z">
            </svg>
        </div>
        <div class="flex-1 flex text-red-500 text-xl font-extrabold justify-between ml-1 hover:text-red-600 hover:font-black">
            <a href="<?= web['url'] ?>" title="<?= web['title'] ?>"><?= web['title'] ?></a>
        </div>
        <label for="menu-toggle" class="pointer-cursor lg:hidden block"><svg class="fill-current text-gray-500" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                <title>menu</title>
                <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path>
            </svg></label>
        <input class="hidden" type="checkbox" id="menu-toggle">

        <div class="hidden lg:flex lg:items-center lg:w-auto w-full" id="menu">
            <nav>
                <ul class="lg:flex items-center justify-between text-base font-semibold text-white pt-4 lg:pt-0">
                    <li>
                        <a class="lg:p-4 py-3 px-0 block border-b-2 border-transparent hover:border-red-500" href="<?= web['url'] ?>/">Home</a>
                    </li>
                    <li>
                        <a class="lg:p-4 py-3 px-0 block border-b-2 border-transparent hover:border-red-500" href="<?= web['url'] ?>/p/contact/">Contact</a>
                    </li>
                    <li>
                        <a class="lg:p-4 py-3 px-0 block border-b-2 border-transparent hover:border-red-500" href="<?= web['url'] ?>/p/copyright/">Copyright</a>
                    </li>
                    <li>
                        <a class="lg:p-4 py-3 px-0 block border-b-2 border-transparent hover:border-red-500" href="<?= web['url'] ?>/p/dmca/">Dmca</a>
                    </li>
                    <li>
                        <a class="lg:p-4 py-3 px-0 block border-b-2 border-transparent hover:border-red-500" href="<?= web['url'] ?>/p/privacy-policy/">Privacy Policy</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>