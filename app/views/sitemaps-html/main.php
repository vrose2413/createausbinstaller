<?php include VIEWS . 'partials/header.php'; ?>

<div class="flex justify-center">
    <div class="w-full lg:w-2/3 my-6 bg-gray-100 border shadow-lg z-10">
        <div class="p-5 text-center m-5">
            <h1 class="text-4xl font-extrabold"><?= $title_post ?></h1>
            <hr class="my-3">
            </br>
            <div class="flex flex-wrap text-left border bg-gray-200 p-4 rounded-lg shadow-inner">
                <?php print_r($contents) ?>
            </div>
        </div>
    </div>

</div>
</div>

<?php include VIEWS . 'partials/footer.php'; ?>