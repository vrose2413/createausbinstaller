<div class="bg-gray-800 p-6 text-center text-white">
    <p class="m-4 p-4">
        <a class="font-semibold" href="<?= web['url'] ?>/feed">RSS Feed</a> | <a class="font-semibold" href="<?= web['url'] ?>/sitemaps/main/">Sitemaps</a>
        <br>
        <span>Copyright &copy; <?= date('Y') ?></span>
    </p>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/js/lightbox.min.js" integrity="sha256-CtKylYan+AJuoH8jrMht1+1PMhMqrKnB8K5g012WN5I=" crossorigin="anonymous"></script>
<script type="text/javascript">
    function init() {
        var imgDefer = document.getElementsByTagName("img");
        for (var i = 0; i < imgDefer.length; i++) {
            if (imgDefer[i].getAttribute("data-src")) {
                imgDefer[i].setAttribute("src", imgDefer[i].getAttribute("data-src"));
            }
        }
    };
    window.onload = init;
</script>

<?php
$floating = file_get_contents(DIRPATH . 'ads/floating.txt');
if ($floating != '') :
?>
    <div id='fixedban' class="flex justify-center" style='width:100%;margin:auto;float:none;overflow:hidden;display:scroll;position:fixed;bottom:0;z-index:999;-webkit-transform:translateZ(0);'>

        <div style='display:block;max-width:728px;height:auto;overflow:hidden;margin:auto'>
            <a class="flex justify-center" id='close-fixedban' onclick='document.getElementById("fixedban").style.display = "none";' style='cursor:pointer;'><img alt='close' src='https://ik.imagekit.io/masjc/close_DwQ5Y1YWa.png' title='close button' style='vertical-align:middle; width: 18px; height: auto; margin-bottom: 2px;' /></a>
            <?= $floating ?>
        </div>
    </div>
<?php endif ?>

</body>

</html>
