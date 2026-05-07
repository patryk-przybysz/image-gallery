<?php

use App\Models\Image;

function format_errors(string $key, array $errors)
{
    if ($key == 'file') {
        $errors[$key] = array_reduce($errors[$key] ?? [], 'array_merge', []);
    }
    if (!empty($errors[$key])) {
        return '<span class="error">' . end($errors[$key]) . '</span>';
    }
}

// https://stackoverflow.com/a/2690541
function time2str($ts)
{
    if (is_int($ts) || (is_string($ts) && ctype_digit($ts))) {
        $ts = (int) $ts;
    } else {
        $ts = strtotime($ts);
    }

    $diff = time() - $ts;
    if ($diff == 0)
        return 'now';
    elseif ($diff > 0) {
        $day_diff = floor($diff / 86400);
        if ($day_diff == 0) {
            if ($diff < 60) return 'just now';
            if ($diff < 120) return '1 minute ago';
            if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
            if ($diff < 7200) return '1 hour ago';
            if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        }
        if ($day_diff == 1) return 'Yesterday';
        if ($day_diff < 7) return $day_diff . ' days ago';
        if ($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
        if ($day_diff < 60) return 'last month';
        return date('F Y', $ts);
    } else {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if ($day_diff == 0) {
            if ($diff < 120) return 'in a minute';
            if ($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
            if ($diff < 7200) return 'in an hour';
            if ($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
        }
        if ($day_diff == 1) return 'Tomorrow';
        if ($day_diff < 4) return date('l', $ts);
        if ($day_diff < 7 + (7 - date('w'))) return 'next week';
        if (ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
        if (date('n', $ts) == date('n') + 1) return 'next month';
        return date('F Y', $ts);
    }
}

function format_time(\MongoDB\BSON\UTCDateTime $date)
{
    return time2str($date->toDateTime()->getTimestamp());
}

function render_image(Image $image)
{
    ob_start();
    include __DIR__ . "/partials/image.php";
    return ob_get_clean();
}
