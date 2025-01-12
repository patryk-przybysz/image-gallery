<?php

namespace App\Utils;

// https://stackoverflow.com/a/4014414
function empty_recursive($var)
{
    if (!is_array($var)) {
        return empty($var);
    }

    foreach ($var as $value) {
        if (!empty_recursive($value)) {
            return false;
        }
    }

    return true;
}


// https://stackoverflow.com/a/173479
function is_associative_array($data)
{
    if (!is_array($data)) {
        return false;
    }
    $keys = array_keys($data);
    return $keys !== array_keys($keys);
}
