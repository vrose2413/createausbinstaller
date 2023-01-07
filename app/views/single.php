<?php include VIEWS . 'partials/header.php'; ?>

<div class="flex justify-center">
    <div class="w-full lg:w-2/3 my-6 bg-gray-100 border shadow-lg z-10">
        <div class="p-5 text-center m-5">
            <!--ads/responsive.txt-->
            <?php if ($top_out != '') {
                print_r("<center>$top_out</center>");
            } ?>

            <h1 class="text-4xl font-extrabold"><?= $title_post ?></h1>
            <hr class="my-3">
            <!--ads/article.txt-->
            <div style="display: block; float: left; margin: 0px 20px 0px 0px;">
                <!--ads/336.txt-->
                <?php if ($top_in != '') {
                    print_r($top_in);
                } ?>
            </div>
            <?php if (count($res) > 3) : ?>
                <p align="justify"><?= $res[0] ?> <?= $res[1] ?></p>
                <p align="justify"><br><?= $res[2] ?> <?= $res[3] ?></p>
            <?php else : ?>
                <p align="justify" id="istext_1"></p>
                <p align="justify" id="istext_2"><br></p>
            <?php endif ?>
            <hr class="my-3">
            <h3 class="text-xl font-bold">Related Posts of <?= $title_post ?> :</h3>
            <br>
            <!--ads/article.txt-->
            <div class="flex justify-center flex-wrap border bg-gray-200 p-4 rounded-lg shadow-inner">
                <?php for ($i = 0; $i < 9; $i++) : ?>
                    <a href="<?= web['url'] . '/' . $fn->slugify($related[$i]) ?>/"><button class="text-white <?= $fn->random_bg() ?>-500 hover:<?= $fn->random_bg() ?>-600 px-4 py-1 text-xs rounded-lg leading-loose mx-1 mb-1 shadow"><?= ucwords($related[$i]) ?></button></a>
                <?php endfor ?>
            </div>
            <!--ads/link.txt-->
        </div>

        <div class="grid md:grid-cols-2 gap-4 mt-6 mb-6 p-2">

            <?php for ($i = 0; $i < 6; $i++) : ?>
                <?php if ($images[$i]['image'] != '') : ?>
                    <div class="border-2 bg-white rounded-lg overflow-hidden shadow-lg">
                        <div class="h-64 overflow-hidden">
                            <a href="<?= $images[$i]["image"] ?>" data-lightbox="roadtrip" data-title="<?= $images[$i]["title"] ?>">
                                <img class="object-cover h-64 w-full transition duration-500 ease-in-out transform hover:-translate-y-1 hover:scale-125" src="https://i.pinimg.com/originals/e3/f6/2f/e3f62f9caff119965b4f54aae69f9eb3.gif" data-src="<?= $images[$i]["image"] ?>" onerror="this.onerror=null;this.src='<?= $images[$i]['thumbnail'] ?>';" width="100%" height="auto" alt="<?= $images[$i]["title"] ?>" loading="lazy" />
                            </a>
                        </div>
                        <div class="p-4 text-center font-semibold hover:font-bold hover:text-blue-500 overflow-hidden">
                            <h3><?= $images[$i]["title"] ?></h3>
                        </div>
                    </div>
                <?php endif ?>
            <?php endfor ?>

        </div>
        <div class="p-5 text-center">
            <hr class="my-3">
            <h2 class="text-xl font-bold"><?= rand(15, 70) ?>+ Images of <?= $title_post ?></h2>
        </div>

        <div class="relative">
            <img class="w-full" src="<?= $images[6]["image"] ?>" onerror="this.onerror=null;this.src='<?= $images[6]['thumbnail'] ?>';" alt="<?= $images[6]["title"] ?>" loading="lazy" />
        </div>

        <?php if (count($res) > 3) {
            echo '<div class="p-5 text-center m-5" id="istext">';
            for ($i = 5; $i < count($res); $i++) {
                echo '<p align="justify">' . $res[$i] . '</p><br>';
            }
            echo '</div>';
        } else {
            echo '<div class="p-5 text-center m-5" id="istext"></div>';
        }
        ?>
        <div class="text-center">
            <hr class="my-3">
            <?php if ($mid_in != '') {
                print_r("<center>$mid_in</center>");
            } ?>

            <h2 class="text-xl font-bold">Gallery of <?= $title_post ?> :</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mt-6 mb-6 p-2">

            <?php for ($i = 8; $i < count($images); $i++) : ?>
                <?php if ($images[$i]['image'] != '' || $images[$i]['image'] != undefined) : ?>
                    <div class="border-2 bg-white rounded-lg overflow-hidden shadow-lg">
                        <div class="h-64 overflow-hidden">
                            <a href="<?= $images[$i]["image"] ?>" data-lightbox="roadtrip" data-title="<?= $images[$i]["title"] ?>">
                                <img class=" object-cover h-64 w-full transition duration-500 ease-in-out transform hover:-translate-y-1 hover:scale-125" src="https://i.pinimg.com/originals/e3/f6/2f/e3f62f9caff119965b4f54aae69f9eb3.gif" data-src="<?= $images[$i]["image"] ?>" onerror="this.onerror=null;this.src='<?= $images[$i]['thumbnail'] ?>';" width="100%" height="auto" alt="<?= $images[$i]["title"] ?>" loading="lazy" />
                            </a>
                        </div>
                        <div class="p-4 text-center font-semibold hover:font-bold hover:text-blue-500 overflow-hidden">
                            <h3><?= $images[$i]["title"] ?></h3>
                        </div>
                    </div>
                <?php endif ?>
            <?php endfor ?>

        </div>


        <div class="p-5 text-center m-5">
            <p align="justify"><strong><a href="<?= web['full_url'] ?>"><?= $title_post ?></a></strong> - The pictures related to be able to <?= $title_post ?> in the following paragraphs, hopefully they will can be useful and will increase your knowledge. Appreciate you for making the effort to be able to visit our website and even read our articles. Cya ~.</p>
        </div>

    </div>

    <?php include VIEWS . 'partials/sidebar.php'; ?>
</div>

<?php if (count($res) < 3) : ?>
    <script>
        let title = '<?= ucwords($query) ?>';
        let query = '<?= $fn->limit_words($query, 6) ?>';
    </script>
    <script src="/public/assets/js/app.js"></script>
<?php endif ?>

<?php include VIEWS . 'partials/footer.php'; ?>
