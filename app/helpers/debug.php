<?php
/**
 * Access to this helper requires loading it into a Controller within the __construct() like so:
 * $this->load_helper(['debug']);
 */

/**
 * @param $array
 * @param $die
 * @return void
 * For debugging output.
 * Use var_dump instead of print_r for more detailed output, including data types
 */
function dd($data, bool $exit = true): void
{
    echo '<pre>', htmlspecialchars(print_r($data, true), ENT_QUOTES, 'UTF-8'), '</pre>';
    if ($exit) {
        exit();
    }
}