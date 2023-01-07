<?php
header('Content-Type: text/plain; charset=UTF-8');
print_r("User-Agent: *
Disallow:

Sitemap: " . web['url'] . "/sitemap.xml");
