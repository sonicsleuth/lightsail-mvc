<?php
/*
 * Access to this helper requires loading it into a Controller within the __construct() like so:
 * $this->load_helper(['language']);
 */

 function language_url($url) {
    if(isset($_COOKIE['language'])) {
        return '/' . $_COOKIE['language'] . $url;
    } else {
        return $url;
    }
 }