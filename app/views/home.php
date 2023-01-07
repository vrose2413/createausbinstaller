<?php include VIEWS . 'partials/header.php'; ?>

<div class="flex justify-center">
    <div class="w-full lg:w-2/3 my-6 bg-gray-100 border shadow-lg z-10">
        <?php if ($top_out != '') : ?>
            <div class="p-5 text-center m-5">
                <?php print_r("<center>$top_out</center>"); ?>
            </div>
        <?php endif ?>
        <div class="grid md:grid-cols-2 gap-4 mt-6 mb-6 p-2">
            <?php for ($i = 0; $i < 20; $i++) : ?>
                <?php if ($images[$i]['image'] != '') : ?>
                    <div class="border-2 bg-white rounded-lg overflow-hidden shadow-lg">
                        <div class="h-64 overflow-hidden">
                            <a href="<?= web['url'] . '/' . $fn->slugify($images[$i]['title']) ?>/">
                                <img src="https://i.pinimg.com/originals/e3/f6/2f/e3f62f9caff119965b4f54aae69f9eb3.gif" data-src="<?= $images[$i]['image'] ?>" width="100%" height="auto" class=" object-cover h-64 w-full transition duration-500 ease-in-out transform hover:-translate-y-1 hover:scale-125" onerror="this.onerror=null;this.src='<?= $images[$i]['thumbnail'] ?>';" alt="<?= ucwords($images[$i]['title']) ?>" loading="lazy" />
                            </a>
                        </div>
                        <div class="p-4 text-center font-semibold hover:font-bold hover:text-blue-500 overflow-hidden">
                            <h2>
                                <a href="<?= web['url'] . '/' . $fn->slugify($images[$i]['title']) ?>/"><?= ucwords($images[$i]['title']) ?></a>
                            </h2>
                        </div>
                    </div>
                <?php endif ?>
            <?php endfor ?>

        </div>
    </div>
    <?php include VIEWS . 'partials/sidebar.php'; ?>
</div>

</div>

<?php include VIEWS . 'partials/footer.php'; ?>