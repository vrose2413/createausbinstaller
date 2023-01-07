<div class="hidden lg:inline-block lg:w-1/5 h-full sticky top-0 my-6 mt-20">
    <!--ads/300.txt-->
    <?php if ($top_side != '') {
        print_r($top_side);
    } ?>
    <div class="text-left text-gray-700 bg-white -ml-2 px-4 py-1 text-base font-semibold leading-loose mb-3">
        Random Posts :
    </div>
    <?php for ($i = 0; $i < 20; $i++) : ?>
        <?php if ($keywords_lists[$i] != '') : ?>
            <a href="<?= web['url'] . '/' . $fn->slugify($keywords_lists[$i]) ?>/">
                <button class="-ml-1 text-left text-white <?= $fn->random_bg() ?>-500 hover:<?= $fn->random_bg() ?>-600 px-4 py-1 text-xs rounded-r-full leading-loose mb-1 shadow-inner">
                    <?= ucwords($keywords_lists[$i]) ?>
                </button>
            </a>
            <br>
        <?php endif ?>
    <?php endfor ?>
</div>