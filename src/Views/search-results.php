<?php

require_once __DIR__ . "/functions.php";

foreach ($images as $image) {
    echo render_image($image);
}
